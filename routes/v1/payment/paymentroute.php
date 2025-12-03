<?php

use App\Http\Controllers\api\v1\payment\PaymentController;
use Illuminate\Support\Facades\Route;


Route::middleware(['api.key', 'access_token'])->controller(PaymentController::class)->group(function () {
  Route::get('/list-products', 'listProducts');
  Route::get('/create-subscription/{product_id}', 'createSubscription');
    Route::get('/get-subscription-status', 'getSubscriptionStatus');
        Route::get('/get-payment-history', 'getPaymentHistory');
});

Route::controller(PaymentController::class)->group(function () {
    Route::post('/dodo-webhook', 'handle');
});
