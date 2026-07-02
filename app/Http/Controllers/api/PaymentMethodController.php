<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index(){
        $payment_methods = PaymentMethod::where('id', '!=', 1)->get();
        return response()->json([
            'status' => true,
            'data' => $payment_methods,
        ]);
    }
}
