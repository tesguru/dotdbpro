<?php

namespace App\Http\Controllers\api\v1\authentication;

use App\Enums\EmailVerificationPurpose;
use App\Enums\OtpCodePurpose;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\CreateAccountRequest;
use App\Http\Requests\Authentication\ForgotPasswordRequest;
use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Requests\Authentication\ResetPasswordRequest;
use App\Services\Authentication\AuthenticationService;
use App\Services\Utility\JWTTokenService;
use App\Models\OtpCode;
use App\Models\UserAccount;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use App\Resources\LoginDataResource;
use App\Services\Utility\MessageService;
use App\Traits\JsonResponseTrait;
use Exception;
use Illuminate\Support\Carbon;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

use Str;

class AuthenticationController extends Controller
{
    use JsonResponseTrait;

          public function __construct()
    {
        $this->auth_service = new AuthenticationService();
    }

    public function testing()
    {
        return $this->successResponse(message: "API Testing");
    }

    public function createAccount(CreateAccountRequest $request)
    {
        try {
            $data = $request->validated();
           $this->auth_service->createAccount($data);
            return $this->successResponse(statusCode: 200, message: "User Account Created Successfully");
        } catch (Exception $ex) {
            return $this->errorResponse(message: $ex->getMessage());
        }
    }
    public function resendEmailVerification(string $email)
    {
        try {
            MessageService::createVerificationLink($email, EmailVerificationPurpose::EMAIL_VERIFICATION->value);
            return $this->successResponse(statusCode: 200, message: "Verification Link Sent");
        } catch (Exception $ex) {
            return $this->errorResponse(500, $ex->getMessage());
        }
    }
public function checkEmail(String $email)
    {
        try {
          $checkEmail = UserAccount::where("email_address", $email)->first();
          if ($checkEmail) {
                 return $this->successResponse(statusCode: 400, message: "Account already exist");
          }
            return $this->successResponse(statusCode: 200, message: "Account dosent exist");
        } catch (Exception $ex) {
            return $this->errorResponse(message: $ex->getMessage());
        }
    }

    public function checkEmailVerfication(String $email)
    {
        try {
          $checkEmail = UserAccount::where("email_address", $email)->first();
          if ($checkEmail->verify_status == false) {
                 return $this->successResponse(statusCode: 400, message: "Email Not Verify");
          }
            return $this->successResponse(statusCode: 200, message: "Email Has been verified");
        } catch (Exception $ex) {
            return $this->errorResponse(message: $ex->getMessage());
        }
    }

    public function checkUsername(String $username)
    {
        try {
          $checkUsername = UserAccount::where("username", $username)->first();
          if ($checkUsername) {
                 return $this->successResponse(statusCode: 400, message: "Account already exist");
          }
            return $this->successResponse(statusCode: 200, message: "Account dosent exist");
        } catch (Exception $ex) {
            return $this->errorResponse(message: $ex->getMessage());
        }
    }

    public function login(LoginRequest $request)
{
    try {
        $data = $request->validated();
        $authenticatedUser = AuthenticationService::loginUser($data);


        $tokenPayload = [
            'email_address' => $authenticatedUser->email,
            'username' => $authenticatedUser->username,
            'user_id' =>$authenticatedUser->user_id
        ];

        return $this->successDataResponse(data: [
            'authToken' => JWTTokenService::generateToken($tokenPayload),
            'refreshToken' => JWTTokenService::generateToken($tokenPayload),
            'userDetails' => new LoginDataResource($authenticatedUser)
        ]);
    } catch (Exception $ex) {
        return $this->errorResponse(message: $ex->getMessage());
    }
}


    public function verifyEmail(Request  $request){

         $token = $request->input('otp');
         $request_type = $request->input('request_type');

           if( $request_type== 'create_account'){
           $verification = OtpCode::whereCode($token)
            ->wherePurpose( OtpCodePurpose::ACCOUNT_CREATION->value)
            ->first();
            }
              if( $request_type == 'forgot_password'){
              $verification = OtpCode::whereCode($token)
            ->wherePurpose( OtpCodePurpose::FORGOT_PASSWORD->value)
            ->first();
            }


        if (!$verification) {
  return $this->successResponse(statusCode: 400, message: "Invalid Verification Code");
        }

        if (Carbon::now()->gt($verification->expires_at)) {
  return $this->successResponse(statusCode: 400, message: "Otp Code Expired");
        }

        $user = UserAccount::where('email_address', $verification->email_address)->first();
        if ($user) {
            $user->verify_status = true;
            $user->verify_date = Carbon::now();
            $user->save();
        }


        $verification->delete();
        return $this->successResponse(statusCode: 200, message: "Verfication Sucessful");
    }


public function checkAuthentication(Request $request){
    try {
    $user = $request->user();
    return $this->successResponse(statusCode: 200, message: "authentication successful");
    } catch (Exception $ex) {
        return $this->errorResponse(message: $ex->getMessage());
    }
    }

      public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $data = $request->validated();
     MessageService::createOTPCode($data['email_address'], OtpCodePurpose::FORGOT_PASSWORD->value);
           return $this->successResponse(statusCode: 200, message: "Otp Send Successfully");
        } catch (Exception $ex) {
            return $this->errorResponse(500, $ex->getMessage());
        }
    }

       public function resendOtp(Request $request)
    {
        try {
            $data = $request->validated();
            if($data['request_type'] == 'create_account'){
            MessageService::createOTPCode($data['email_address'], OtpCodePurpose::ACCOUNT_CREATION->value);
            }
              if($data['request_type'] == 'forgot_password'){
            MessageService::createOTPCode($data['email_address'], OtpCodePurpose::FORGOT_PASSWORD->value);
            }

           return $this->successResponse(statusCode: 200, message: "Otp Send sucessfully");
        } catch (Exception $ex) {
            return $this->errorResponse(500, $ex->getMessage());
        }
    }


     public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $data = $request->validated();
            AuthenticationService::updateUserPassword($data);
            return $this->successResponse(message: "Password reset successfully");
        } catch (Exception $ex) {
            return $this->errorResponse(500, $ex->getMessage());
        }
    }

    public function redirectToGoogle()
{
    return Socialite::driver('google')->stateless()->redirect();
}

public function handleGoogleCallback()
{
    $googleUser = Socialite::driver('google')->stateless()->user();

    $user = UserAccount::updateOrCreate(
        ['email_address' => $googleUser->getEmail()],
        [
            'username' => $googleUser->getName(),
            'verify_status' => true,
            'verified_at' => now(),
            'sign_up_type' => "google_auth",
            'password' => bcrypt(Str::random(24)),
        ]
    );

    $token = JWTTokenService::generateToken([
        'email_address' => $user->email_address,
        'username' => $user->username,
    ]);

    $frontendUrl = config('app.frontend_url');
return redirect()->to($frontendUrl . '/auth/callback?' . http_build_query([
    'token' => $token,
    'username' => $user->username,
    'email' => $user->email_address,
]));
}


public function handle(Request $request)
{
    $data = $request->all();
    Log::info('Dodo webhook received', $data);

    $payload = $data['data'] ?? [];

    DB::beginTransaction();

    try {
        // === Subscription events ===
        if (($payload['payload_type'] ?? null) === 'Subscription') {
            switch ($data['type']) {
                case 'subscription.active':
                case 'subscription.renewed':
                    Subscription::updateOrCreate(
                        ['subscription_id' => $payload['subscription_id']],
                        [
                            'user_id' => UserAccount::where('email_address', $payload['customer']['email'] ?? null)->value('user_id'),
                            'status' => $payload['status'] ?? 'active',
                            'product_id' => $payload['product_id'] ?? null,
                            'currency' => $payload['currency'] ?? null,
                            'amount' => $payload['recurring_pre_tax_amount'] ?? null,
                            'payment_frequency_count' => $payload['payment_frequency_count'] ?? null,
                            'payment_frequency_interval' => $payload['payment_frequency_interval'] ?? null,
                            'subscription_period_count' => $payload['subscription_period_count'] ?? null,
                            'subscription_period_interval' => $payload['subscription_period_interval'] ?? null,
                            'next_billing_date' => $payload['next_billing_date'] ?? null,
                            'previous_billing_date' => $payload['previous_billing_date'] ?? null,
                            'expires_at' => $payload['expires_at'] ?? null,
                            'raw_payload' => json_encode($payload), // ensure JSON storage
                        ]
                    );
                    Log::info('Subscription saved', $payload);
                    break;

                case 'subscription.cancelled':
                    Subscription::where('subscription_id', $payload['subscription_id'])
                        ->update([
                            'status' => 'cancelled',
                            'raw_payload' => json_encode($payload),
                        ]);
                    Log::info('Subscription cancelled', $payload);
                    break;
            }
        }

        // === Payment events ===
        if (($payload['payload_type'] ?? null) === 'Payment') {
            if ($data['type'] === 'payment.succeeded') {
                $customer = $payload['customer'] ?? [];

                Payment::updateOrCreate(
                    ['payment_id' => $payload['payment_id']],
                    [
                        'user_id' => UserAccount::where('email_address', $customer['email'] ?? null)->value('user_id'),
                        'subscription_id' => $payload['subscription_id'] ?? null,
                        'business_id' => $payload['business_id'] ?? null,
                        'status' => $payload['status'] ?? null,
                        'total_amount' => $payload['total_amount'] ?? null,
                        'currency' => $payload['currency'] ?? null,
                        'payment_method' => $payload['payment_method'] ?? null,
                        'card_last_four' => $payload['card_last_four'] ?? null,
                        'card_type' => $payload['card_type'] ?? null,
                        'card_network' => $payload['card_network'] ?? null,
                        'customer_id' => $customer['customer_id'] ?? null,
                        'customer_name' => $customer['name'] ?? null,
                        'customer_email' => $customer['email'] ?? null,
                        'raw_payload' => json_encode($payload), // ensure JSON storage
                    ]
                );

                Log::info('Payment succeeded and saved', $payload);
            }
        }

        DB::commit();

        return response()->json(['status' => 'ok']);
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Webhook handling failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'payload' => $payload,
        ]);

        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}

}


