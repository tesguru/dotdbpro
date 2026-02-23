<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Authentication\AuthenticationService;
use App\Services\Utility\MessageService;
use App\Models\OtpCode;
use App\Models\UserAccount;
use App\Enums\OtpCodePurpose;
use Illuminate\Support\Carbon;
use Exception;

class AuthController extends Controller
{
    protected $auth_service;

    public function __construct()
    {
        $this->auth_service = new AuthenticationService();
    }

    // POST /sign-up
    public function register(Request $request)
    {
        $request->validate([
            'username'             => 'required|string|min:3|unique:user_accounts,username',
            'email_address'        => 'required|email|unique:user_accounts,email_address',
            'phone_number'         => 'required|string|min:10',
            'password'             => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
            'terms'                => 'accepted',
        ]);

        try {
            $this->auth_service->createAccount($request->all());

            // Send OTP after registration
            MessageService::createOTPCode(
                $request->email_address,
                OtpCodePurpose::ACCOUNT_CREATION->value
            );

            // Redirect to verify email and pass email as prop
            return Inertia::render('Auth/VerifyEmail', [
                'email' => $request->email_address,
            ]);

        } catch (Exception $ex) {
            return back()->withErrors(['email_address' => $ex->getMessage()]);
        }
    }

    // POST /sign-in
  // POST /sign-in
public function lddogin(Request $request)
{
    $request->validate([
        'email_address' => 'required|email',
        'password'      => 'required',
    ]);

    try {
        $user = AuthenticationService::loginUser($request->all());
        Auth::login($user, $request->boolean('remember'));

     dd($user);
        return redirect('/');

    } catch (AuthenticationException $ex) {
        // Wrong password or email
        return back()->withErrors([
            'email_address' => $ex->getMessage()
        ]);

    } catch (Exception $ex) {
        // Not verified — send to verify page with email as prop
        return Inertia::render('Auth/VerifyEmail', [
            'email' => $request->email_address,
        ]);
    }
}

public function login(Request $request)
{
    $request->validate([
        'email_address' => 'required|email',
        'password'      => 'required',
    ]);

    $user = AuthenticationService::loginUser($request->all());

    if (!$user) {
        return back()->withErrors([
            'email_address' => 'Invalid credentials.'
        ]);
    }

    dd(Auth::login($user, $request->boolean('remember')));

    if (!Auth::check()) {
        return Inertia::render('Auth/VerifyEmail', [
            'email' => $request->email_address,
        ]);
    }

    return redirect('/');
}

    // POST /verify-email
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'otp'          => 'required|string|size:6',
            'email'        => 'required|email',
            'request_type' => 'required|string',
        ]);

        try {
            $purpose = $request->request_type === 'create_account'
                ? OtpCodePurpose::ACCOUNT_CREATION->value
                : OtpCodePurpose::FORGOT_PASSWORD->value;

            $verification = OtpCode::whereCode($request->otp)
                ->wherePurpose($purpose)
                ->first();

            if (!$verification) {
                return back()->withErrors(['otp' => 'Invalid verification code']);
            }

            if (Carbon::now()->gt($verification->expires_at)) {
                return back()->withErrors(['otp' => 'OTP code has expired']);
            }

            // Mark user as verified
            $user = UserAccount::where('email_address', $verification->email_address)->first();
            if ($user) {
                $user->verify_status = true;
                $user->verify_date = Carbon::now();
                $user->save();
            }

            $verification->delete();

            return redirect('/sign-in')->with('success', 'Account verified! Please sign in.');

        } catch (Exception $ex) {
            return back()->withErrors(['otp' => $ex->getMessage()]);
        }
    }

    // POST /verify-email-forgot
    public function verifyEmailForgot(Request $request)
    {
        $request->validate([
            'otp'          => 'required|string',
            'email_address' => 'required|email',
            'request_type' => 'required|string',
        ]);

        try {
            $verification = OtpCode::whereCode($request->otp)
                ->wherePurpose(OtpCodePurpose::FORGOT_PASSWORD->value)
                ->first();

            if (!$verification) {
                return back()->withErrors(['otp' => 'Invalid verification code']);
            }

            if (Carbon::now()->gt($verification->expires_at)) {
                return back()->withErrors(['otp' => 'OTP code has expired']);
            }

            $verification->delete();

            // Redirect to reset password with email as prop
            return Inertia::render('Auth/ResetPassword', [
                'email' => $request->email_address,
            ]);

        } catch (Exception $ex) {
            return back()->withErrors(['otp' => $ex->getMessage()]);
        }
    }

    // POST /forgot-password
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email_address' => 'required|email|exists:user_accounts,email_address',
        ]);

        try {
            MessageService::createOTPCode(
                $request->email_address,
                OtpCodePurpose::FORGOT_PASSWORD->value
            );

            // Pass email as Inertia prop — no encrypted URLs!
            return Inertia::render('Auth/VerifyEmailForgot', [
                'email' => $request->email_address,
            ]);

        } catch (Exception $ex) {
            return back()->withErrors(['email_address' => $ex->getMessage()]);
        }
    }

    // POST /reset-password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email_address'   => 'required|email',
            'password'        => 'required|min:8',
            'confirmPassword' => 'required|same:password',
        ]);

        try {
            AuthenticationService::updateUserPassword($request->all());

            return redirect('/sign-in')->with('success', 'Password reset successfully!');

        } catch (Exception $ex) {
            return back()->withErrors(['password' => $ex->getMessage()]);
        }
    }

    // POST /resend-otp
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            // Try create_account first, fallback to forgot_password
            MessageService::createOTPCode(
                $request->email,
                OtpCodePurpose::ACCOUNT_CREATION->value
            );

            return back()->with('success', 'OTP sent successfully');

        } catch (Exception $ex) {
            return back()->withErrors(['email' => $ex->getMessage()]);
        }
    }

    // POST /logout
    public function logout()
    {
        Auth::logout();
        return redirect('/sign-in');
    }
}
