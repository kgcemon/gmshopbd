<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\PaymentSms;
use App\Models\Product;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function addOrder(Request $request)
    {
        // Step 1: Basic validation rules
        $rules = [
            'product_id'     => 'required|exists:products,id',
            'item_id'        => 'required|exists:items,id',
            'customer_data'  => 'required',
            'payment_id'     => 'required|exists:payment_methods,id',
            'transaction_id' => 'nullable|string',
            'payment_number' => 'nullable|string',
        ];

        $validated = $request->validate($rules);

        $user          = Auth::user();
        $product       = Product::find($validated['product_id']);
        $item          = Item::find($validated['item_id']);
        $paymentMethod = PaymentMethod::find($validated['payment_id']);

        if (!$product || !$item || !$paymentMethod) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid product, item, or payment method',
            ], 404);
        }

        try {

            return DB::transaction(function () use ($validated, $user, $product, $item, $paymentMethod, $request) {

                // Duplicate trxID চেক (only if provided)
                if (!empty($validated['transaction_id'])) {
                    $checkDuplicate = Order::where('transaction_id', $validated['transaction_id'])->count();
                    if ($checkDuplicate > 0) {
                        return response()->json([
                            'status'  => false,
                            'message' => 'This transaction ID is already used.',
                        ], 409);
                    }
                }

                $order = new Order();
                $order->quantity      = 1;
                $order->total         = $item->price;
                $order->product_id    = $validated['product_id'];
                $order->item_id       = $validated['item_id'];
                $order->customer_data = $validated['customer_data'];
                $order->payment_method = $validated['payment_id'];

                if ($user) {
                    $order->user_id = $user->id;
                    $order->name    = $user->name;
                    $order->email   = $user->email;
                } else {
                    $order->user_id = null;
                    $order->name    = "guest";
                }

                // ✅ Wallet Payment
                if ($paymentMethod->method === 'Wallet') {
                    if (!$user) {
                        return response()->json([
                            'status'  => false,
                            'message' => "Wallet payment requires login.",
                        ], 401);
                    }

                    if ($user->wallet < $item->price) {
                        return response()->json([
                            'status'  => false,
                            'message' => "আপনার ওয়ালেটে যথেষ্ট টাকা নেই। দয়া করে টাকা এড করে আবার চেষ্টা করুন।",
                        ]);
                    }

                    $user->wallet -= $item->price;
                    WalletTransaction::create([
                        'user_id'   => $user->id,
                        'amount'    => $item->price,
                        'type'      => 'debit',
                        'description' => "Order for $item->name",
                        'status'    => 1,
                    ]);
                    $user->save();
                    $order->status = 'processing';

                } else {
                    $paySMS = null;
                    if (!empty($validated['transaction_id'])) {
                        $paySMS = PaymentSms::where('trxID', $validated['transaction_id'])
                            ->where('amount', '>=', (integer)$item->price)
                            ->where('status', 0)
                            ->first();
                    }

                    if ($paySMS != null) {
                        $order->transaction_id = $paySMS->trxID;
                        $order->number         = $paySMS->number;
                        $paySMS->status = 1;
                        $paySMS->save();
                        $order->status         = 'processing';
                    } else {
                        if (empty($validated['transaction_id']) || empty($validated['payment_number'])) {
                            return response()->json([
                                'status'  => false,
                                'message' => 'Transaction ID and payment number are required for this payment method.',
                            ], 422);
                        }

                        $order->transaction_id = $validated['transaction_id'];
                        $order->number         = $validated['payment_number'];
                        $order->status         = 'hold';
                    }
                }

                $order->save();

                return response()->json([
                    'status'  => true,
                    'message' => 'Order created successfully',
                    'order'   => $order,
                ], 201);

            });

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function myOrders()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $orders = Order::where('user_id',$user->id)->orderBy('id', 'desc')->paginate(10);
            return view('user.my-order', compact('orders'));
        }
    }

    public function orderView($id){
        if (Auth::check()) {
            $user = Auth::user();
            $order = Order::where('user_id',$user->id)->where('id',$id)->first();
            if ($order) {
                return view('user.order-view', compact('order'));
            }
            return view('errors.404');
        }
    }


    public function thankYouPage($uid)
    {
        $order = Order::where('uid',$uid)->first();
        if ($order) {
            return view('user.thank-you',compact('order'));
        }
        return view('user.home');
    }
}
