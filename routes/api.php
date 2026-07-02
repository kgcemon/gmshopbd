<?php

use App\Http\Controllers\admin\PaymentSMSController;
use App\Http\Controllers\api\auth\AuthController;
use App\Http\Controllers\api\auth\UserOrderController;
use App\Http\Controllers\api\CategoriesController;
use App\Http\Controllers\api\GiveawayController;
use App\Http\Controllers\api\HomePageController;
use App\Http\Controllers\api\OrdersController;
use App\Http\Controllers\api\PaymentMethodController;
use App\Http\Controllers\api\ProductsController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\WebHooksController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//auth user
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('my-orders',[UserOrderController::class,'userOrder'])->middleware('auth:sanctum');
Route::get('my-profile',[AuthController::class,'user'])->middleware('auth:sanctum');


//login register
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'loginWithGoogleToken']);


//product
Route::get('/products',[ProductsController::class, 'index']);
Route::get('/product/{slug}',[ProductsController::class, 'show']);

//categories
Route::get('categories',[CategoriesController::class, 'index']);

//imagesSlider
Route::get('slider-image',[HomePageController::class,'index']);

//paymentMethod
Route::get('payment-method',[PaymentMethodController::class,'index']);

//giveaway
Route::get('giveaway',[GiveawayController::class,'index']);

//order
Route::post('add-order',[OrdersController::class, 'store']);
Route::post('my-orders',[UserOrderController::class,'userOrder']);

//update Notice
Route::get('notice',[HomePageController::class,'notice']);
Route::get('help-line',[HomePageController::class,'helpLine']);

//webhooks
Route::post('auto-webhooks',[WebHooksController::class,'OrderUpdate']);

//receiveSMSWhook
Route::post('store-sms',[PaymentSMSController::class,'SmsWhooks']);

Route::get('/player-info/{uid}', [OfferController::class, 'getPlayerInfo']);
