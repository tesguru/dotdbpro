<?php

use Inertia\Inertia;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;

// Landing & Search
Route::get('/test', function () {
    return inertia('Test');
});
Route::get('/', [HomeController::class, 'index']);

Route::get('/debug-auth', function () {
    return response()->json([
        'user' => auth()->user(),
        'check' => auth()->check(),
        'id' => auth()->id(),
        'session' => session()->all(),
    ]);
});

Route::get('/sign-in', fn() => Inertia::render('Auth/SignIn'));
Route::get('/sign-up', fn() => Inertia::render('Auth/SignUp'));
Route::get('/forgot-password', fn() => Inertia::render('Auth/ForgotPassword'));
Route::get('/reset-password', fn() => Inertia::render('Auth/ResetPassword'));
Route::get('/verify-email', fn() => Inertia::render('Auth/VerifyEmail'));
Route::get('/verify-email-forgot', fn() => Inertia::render('Auth/VerifyEmailForgot'));


Route::post('/sign-in', [AuthController::class, 'login']);
Route::post('/sign-up', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
Route::post('/verify-email-forgot', [AuthController::class, 'verifyEmailForgot']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/logout', [AuthController::class, 'logout']);


Route::middleware(['auth'])->group(function () {
    Route::get('/admin-dashboard', fn() => Inertia::render('Admin/Dashboard'));
});


Route::get('/blog', fn() => Inertia::render('Blog/Index'));
Route::get('/blog/{slug}', fn() => Inertia::render('Blog/Single'));
