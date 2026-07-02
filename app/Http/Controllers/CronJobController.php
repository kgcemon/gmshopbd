<?php

namespace App\Http\Controllers;

use App\Mail\SendPinsMail;
use App\Models\Api;
use App\Models\Code;
use App\Models\Order;
use App\Models\PaymentSms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CronJobController extends Controller
{
    public function freeFireAutoTopUpJob()
    {
        $orders = Order::where('status', 'processing')->whereNull('order_note')->limit(4)->get();
        $denomsForShell = ["108593", "108592", "108591", "108590", "108589", "108588", "LITE", "3D", "7D", "30D"];

        try {
            foreach ($orders as $order) {
                DB::beginTransaction();

                if (in_array($order->item->denom, $denomsForShell)) {
                    $success = $this->shellsTopUp($order);
                    if ($success) {
                        DB::commit();
                    } else {
                        DB::rollBack();
                    }
                    continue;
                }



                if ($order->item->type === "social"){
                    $success = $this->social($order);
                    if ($success) {
                        DB::commit();
                    } else {
                        DB::rollBack();
                    }
                    continue;
                }

                if ($order->item->denom === "2000") {
                    $success = $this->sendGiftCard($order);
                    if ($success) {
                        DB::commit();
                    } else {
                        DB::rollBack();
                    }

                    continue;
                }

                $order = Order::lockForUpdate()->find($order->id);

                if ($order->status !== 'processing' || $order->order_note !== null) {
                    DB::rollBack();
                    continue;
                }

                $denom = (string) $order->item->denom ?? '';

                if ($denom == null) {
                    DB::rollBack();
                    continue;
                }

                $denoms = explode(',', $denom);


                // Count input requirements (কতবার কোন denom দরকার)
                $counts = array_count_values($denoms);

                $missing = [];

                foreach ($counts as $value => $needed) {
                    $available = Code::where('denom', $value)->where('status', 'unused')
                        ->count();

                    if ($available < $needed) {
                        $missing[$value] = [
                            'needed'    => $needed,
                            'available' => $available
                        ];
                    }
                }


                if ($missing) {
                    DB::rollBack();
                    continue;
                }
                $apiData = Api::where('type', 'auto')->where('status', 1)->first();
                if (!$apiData) {
                    DB::rollBack();
                    continue;
                }
                foreach ($denoms as $d) {
                  $int = 10;
                  $int++;
                    $uid = bin2hex(random_bytes($int));
                    $code = Code::where('denom', $d)->where('status', 'unused')
                        ->lockForUpdate()
                        ->first();

                    if (!$code) {
                        DB::rollBack();
                        continue;
                    }
                    $type = (Str::startsWith($code->code, 'UPBD')) ? 2 : ((Str::startsWith($code->code, 'BDMB')) ? 1 : 1);

                    try {
                        $response = Http::withHeaders([
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                            'RA-SECRET-KEY' => $apiData->key,
                        ])->post($apiData->url, [
//                            "playerId"   => $order->customer_data,
//                            "denom"      => $d,
//                            "type"       => $type,
//                            "voucherCode"=> $code->code,
//                            "webhook"    => "https://gmshopbd.com/api/auto-webhooks",

                            "playerid" => "$order->customer_data",
                            "pacakge" => $this->denomToPkge($d),
                            "code" => "$code->code",
                            "orderid" => $uid,
                            "url" => "https://gmshopbd.com/api/auto-webhooks",
                            "tgbotid" => "701657976",
                            "shell_balance" => 28,
                            "ourstock" => 1
                        ]);

                    }catch (\Exception $exception){$order->order_note = 'server error';}

                    $data = $response->json();
                    $order->status = 'Delivery Running';
                    $order->order_note = $uid;
                    $order->save();
                    $code->status = 'used';
                    $code->uid = $uid;
                    $code->order_id = $order->id;
                    $code->active = 0;
                    $code->save();
                }

                DB::commit();
            }

            return 'Cron job run successfully';
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }

    }

    private function sendGiftCard($order): bool
    {
        // Lock the order row
        $order = Order::lockForUpdate()->find($order->id);

        if (!$order || $order->status === 'delivered') {
            return false; // already processed
        }

        DB::beginTransaction();
        try {
            $email = $order->email;
            $total = Code::where('item_id', $order->item_id)
                ->where('status', 'unused')
                ->lockForUpdate()
                ->count();

            if ($total < $order->quantity || !$email) {
                DB::rollBack();
                return false;
            }

            $codes = Code::where('item_id', $order->item_id)
                ->where('status', 'unused')
                ->lockForUpdate()
                ->limit($order->quantity)
                ->get();

            if ($codes->isEmpty()) {
                DB::rollBack();
                return false;
            }

            $pins = $codes->map(function ($code) use ($order) {
                return [
                    'pin'    => $code->code,
                    'name'   => $order->item->name,
                ];
            })->toArray();

            // Update codes
            Code::whereIn('id', $codes->pluck('id'))->update([
                'status'   => 'used',
                'order_id' => $order->id,
            ]);

            // Update order
            $order->status = 'delivered';
            $order->save();

            DB::commit();

            // Mail send after commit
            try {
                Mail::to($email)->send(new SendPinsMail($order->name ?? 'Customer', $pins));
            } catch (\Exception $exception) {
                \Log::error("SendPinsMail failed for order {$order->id}: {$exception->getMessage()}");
            }

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }


    public function shellsTopUp($order): bool
    {
        $denom = (string) $order->item->denom ?? '';
        $apiData = Api::where('type', 'shell')->where('status', 1)->first();
        if (!$apiData) {
            DB::rollBack();
            return false;
        }
        $url =  'http://15.235.147.112:3333/complete';
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ],)->post($apiData->url,[
                "playerid" => "$order->customer_data",
                "pacakge" => "$denom",
                "code" => "shell",
                "orderid" => $order->id,
                "url" => "https://gmshopbd.com/api/auto-webhooks",
                "username" => "643356853",
                "password" => "Sozib2500@",
                "autocode" => "LOVTJGVQCLS5WW6K",
                //"username" => "668106777",
                //"password" => "gmshopbd345@",
                //"autocode" => "REX7FSM44ANMSP2S",
                "tgbotid" => "701657976",
                "shell_balance" => 28,
                "ourstock" => 1
            ]);
        }catch (\Exception $exception){
            return false;
        }
        if ($response->successful()) {
            $order->order_note = $order->id;
            $order->status = 'Delivery Running';
            $order->save();
            return true;
        }
        return false;
    }


private function social($order): bool
{

  $service = $order->item->denom;
  $link = $order->customer_data;
  $quantity = $order->item->description;

    $response = Http::post("https://smmprovider.co/api/v2", [
        "key"      => "82c65db32312e62248faf06e9a59b48b",
        "action"   => "add",
        "service"  => "$service",
        "link"     => "$link",
        "quantity" => "$quantity",
        "runs"     => "5",
        "interval" => "30",
    ]);

    $data = $response->json();

    if ($response->successful() && isset($data['order'])) {

        $order->status = 'Delivery Running';
        $order->order_note = $data['order']; // 120409697
        $order->save();

        DB::commit();
        return true;

    } else {

        $order->status = 'Delivery Running';
        $order->order_note = $data['message'] ?? 'Unknown error';
        $order->save();

        DB::commit();
        return false;
    }
}

    public function denomToPkge($denom)
    {
        if ($denom == 0) {
            return 25;
        }
        if ($denom == 1) {
            return 50;
        }elseif ($denom == 2) {
            return 115;
        }elseif ($denom == 3) {
            return 240;
        }elseif ($denom == 4) {
            return 610;
        }elseif ($denom == 5) {
            return 1240;
        }elseif ($denom == 6) {
            return 1625;
        }elseif ($denom == 7) {
            return 161;
        }elseif ($denom == 8) {
            return 800;
        }

        return null;
    }

    public function checkPendingPaymentSMS()
    {
        $allSms = PaymentSms::where('status', 0)->get();

        foreach ($allSms as $sms) {

            // find matching processing order
            $order = Order::where('transaction_id', $sms->trxID)
                ->where('status', 'hold')
                ->first();

            // if no order found, continue loop
            if (!$order) {
                continue;
            }

            // amount matched or greater?
            if ($order->total <= $sms->amount) {
                // update sms
                $sms->status = 1;
                $sms->save();

                // update order
                $order->status = 'processing';   // ✔ recommended
                $order->save();
            }
        }

        return $allSms;
    }


}
