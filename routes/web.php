<?php

use App\Http\Controllers\admin\ApiController;
use App\Http\Controllers\admin\CategoriesController;
use App\Http\Controllers\admin\CodesController;
use App\Http\Controllers\admin\HelpLineControllers;
use App\Http\Controllers\admin\PaymentMethodSettingController;
use App\Http\Controllers\admin\PaymentSMSController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ReviewControllers;
use App\Http\Controllers\admin\SliderController;
use App\Http\Controllers\admin\UsersController;
use App\Http\Controllers\admin\VariantController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CronJobController;
use App\Http\Controllers\NoticeUpdateController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\user\DepositController;
use App\Http\Controllers\user\FreeFireLikeController;
use App\Http\Controllers\user\OrderController;
use App\Http\Controllers\user\ReviewController;
use App\Http\Controllers\user\SiteHomeScreenController;
use App\Http\Controllers\user\SiteProductsScreenController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\OrdersController;
use App\Http\Controllers\ProfileController;

Route::get('auto-top-up-cron',[CronJobController::class,'freeFireAutoTopUpJob']);

Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/sitemap-pages.xml', [SitemapController::class, 'pages']);
Route::get('/sitemap-products.xml', [SitemapController::class, 'products']);
Route::get('/review/{slug}', [ReviewController::class, 'reviewByProduct'])->name('review');
Route::get('/add-review/{slug}', [ReviewController::class, 'show']);
Route::post('/add-review', [ReviewController::class, 'store'])->name('review.store');

//admin
Route::middleware('guest')->group(function () {
    Route::get('admin/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('adminLogin');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});


Route::get('/auth/google/redirect', [GoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);


Route::get('/', [SiteHomeScreenController::class, 'index'])->name('home');
Route::get('/product/{slug}', [SiteProductsScreenController::class, 'index'] )->name('product');
Route::post('add-order', [OrderController::class, 'addOrder'])->name('addOrder');
Route::get('thank-you/{uid}', [OrderController::class, 'thankYouPage'])->name('thankYouPage');

// Profile Page
Route::get('profile', [ProfileController::class, 'show'])->middleware('auth:web')->name('profile');
Route::get('my-orders', [OrderController::class, 'myOrders'])->middleware('auth:web')->name('myOrders');
Route::get('order/{id}', [OrderController::class, 'orderView'])->middleware('auth:web')->name('orderView');
Route::get('deposit',[DepositController::class, 'deposit'])->middleware('auth:web')->name('deposit');
Route::post('add-money',[DepositController::class, 'depositStore'])->middleware('auth:web');

// Authenticated Admin Routes
Route::middleware('auth:admin')->prefix('admin')->as('admin.')->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', ProductController::class);


    // Orders
    Route::get('orders', [\App\Http\Controllers\admin\OrdersController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [\App\Http\Controllers\admin\OrdersController::class, 'show'])->name('orders.show');
    Route::put('orders/{order}', [\App\Http\Controllers\admin\OrdersController::class, 'update'])->name('orders.update');
    Route::get('admin/orders/{order}', [\App\Http\Controllers\admin\OrdersController::class, 'show'])->name('admin.orders.show');
    Route::post('admin/orders/update/{id}', [\App\Http\Controllers\admin\OrdersController::class, 'edit'])->name('orders.edits');
    Route::get('admin/orders/edit/{id}', [\App\Http\Controllers\admin\OrdersController::class, 'editFrom'])->name('orders.edit');
    Route::post('/admin/orders/bulk-action', [OrdersController::class, 'bulkAction'])->name('orders.bulkAction');


    Route::resource('variant', VariantController::class);
    Route::resource('categories', CategoriesController::class);
    Route::get('variants/{id}', [VariantController::class, 'variant'])->name('variant');


    Route::resource('sliders', SliderController::class);
    Route::resource('users', UsersController::class);


    //codes
    Route::resource('codes', CodesController::class);
    Route::get('codes/{id}', [CodesController::class, 'code'])->name('code');
    Route::get('code/{id}', [CodesController::class, 'singleCode']);


    //payment Setting
    Route::resource('payment-methods', PaymentMethodSettingController::class);
    Route::post('payment-methods/{id}/toggle-status', [PaymentMethodSettingController::class, 'toggleStatus'])->name('payment-methods.toggleStatus');
    Route::post('payment-methods/{id}/copy', [PaymentMethodSettingController::class, 'copyNumber'])->name('payment-methods.copy');


    //paymentSMS
    Route::get('/payment-sms', [PaymentSmsController::class, 'index'])->name('sms');
    Route::post('/sms/add', [PaymentSmsController::class, 'addSms'])->name('sms.add');
    Route::put('/sms/update-status', [PaymentSmsController::class, 'updateStatus'])->name('sms.update-status');
    Route::delete('/sms/{id}', [PaymentSmsController::class, 'delete'])->name('sms.delete');


    //offer
    Route::get('/send-offer', [OfferController::class, 'index'])->name('offer.index');
    Route::post('/send-offer', [OfferController::class, 'send'])->name('offer.sends');

    //notice update
    Route::get('/notice', [NoticeUpdateController::class, 'index'])->name('notice.index');
    Route::post('/notice/store', [NoticeUpdateController::class, 'store'])->name('notice.store');
    Route::delete('/notice/{id}', [NoticeUpdateController::class, 'destroy'])->name('notice.destroy');

    //apis
    Route::get('/apis', [ApiController::class, 'index'])->name('apis.index');       // List APIs
    Route::post('/apis', [ApiController::class, 'store'])->name('apis.store');      // Add API
    Route::put('/apis/{api}', [ApiController::class, 'update'])->name('apis.update'); // Update API
    Route::delete('/apis/{api}', [ApiController::class, 'destroy'])->name('apis.destroy');

    //review
    Route::resource('reviews', ReviewControllers::class)->except(['create','show','edit']);

    //helpline
    Route::resource('helpline', HelpLineControllers::class)->except(['create','show','edit']);

    //user transaction
    Route::get('user-transaction/{id}',[UsersController::class, 'walletTransactions']);

});

// Fallback Route for 404
//Route::fallback(function () {
//    return redirect()->route('admin.dashboard')->with('error', 'Page not found.');
//});


Route::get('about', [SiteHomeScreenController::class, 'aboutUs'])->name('aboutUs');
Route::get('privacy', [SiteHomeScreenController::class, 'privacyPolicy'])->name('policy');
Route::get('privacy-policy', [SiteHomeScreenController::class, 'privacyPolicyApp'])->name('policyApp');
Route::get('free-fire-free-like-daily',[FreeFireLikeController::class,'index'])->name('freeFireLikeDaily');
Route::post('send-like',[FreeFireLikeController::class,'sendLike'])->name('player.submit');
Route::get('terms', function (){
    return view('user.terms');
});

Route::get('checkPending', [CronJobController::class, 'checkPendingPaymentSMS']);

Route::get('/{any}', function($any = null) {
    if(!in_array(request()->path(), ['css', 'js', 'images', 'manifest.json', 'service-worker.js'])) {
        abort(404); // Laravel Blade 404 trigger হবে
    }
    return view('user.master');
})->where('any', '.*');


