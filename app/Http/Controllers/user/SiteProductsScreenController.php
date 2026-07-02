<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class SiteProductsScreenController extends Controller
{
    public function index($slug) {
        try {

            $cacheKey = 'product_'.$slug;

            $product = Cache::rememberForever($cacheKey, function() use ($slug) {
                return Product::with('items')->where('slug', $slug)->first();
            });

            if (auth()->check()) {
                $payment = PaymentMethod::all();
            } else {
                $payment = PaymentMethod::where('method', '!=', 'Wallet')->get();
            }

            if ($product) {
                return view('user.product', compact('product', 'payment'));
            } else {
                return view('errors.404');
            }

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
