<?php

use App\Http\Controllers\api\v1\authentication\AuthenticationController as AuthenticationAPIController;
use Illuminate\Support\Facades\Route;


Route::middleware()->controller(AuthenticationAPIController::class)->group(function () {
    Route::get('/test', 'testing');
    Route::post('/verify-email',  'verifyEmail');
    Route::post('/create-account', 'createAccount');
      Route::post('/forgot-password', 'forgotPassword');
    Route::get('/check-email/{email}', 'checkEmail');
    Route::get('/check-username/{username}', 'checkUsername');
    Route::get('/check-email-verification/{email}', 'checkEmailVerfication');
    Route::post('/reset-password', 'resetPassword');
      Route::get('/resend-email-verification/{email}', 'resendEmailVerification');
       Route::get('/check-token', 'checkAuthentication');
    Route::post('/login', 'login');
       Route::get('/check-jwt-token', 'checkJwtToken');

});
  Route::controller(AuthenticationAPIController::class)->group(function () {
    Route::get('/auth/google/redirect', 'redirectToGoogle');
    Route::get('/auth/google/callback', 'handleGoogleCallback');
});
Route::post('/dodo-webhook', [AuthenticationAPIController::class, 'handle'])
    ->middleware('api.key','verify.forwarder.token');
