<?php

namespace App\Services\Authentication;
use App\Enums\OtpCodePurpose;
use App\Models\OtpCode;
use App\Models\UserAccount;
use App\Services\Utility\MessageService;
use App\Services\Utility\DodoPaymentService;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class AuthenticationService
{
         public function __construct()
    {
        $this->service = new DodoPaymentService();
    }
     public  function createAccount(array $data)
    {
        $data['password'] = Hash::make($data['password']);

        $data['sign_up_type'] = "manual";
        $dodoRegistration = $this->service->createCustomer($data);
        $data['dodo_customer_id'] = $dodoRegistration['customer_id'];
         $customer = UserAccount::create($data);
        MessageService::createOTPCode($data['email_address'], OtpCodePurpose::ACCOUNT_CREATION->value);
         return $customer;
    }
    public static function loginUser(array $data)
    {
        $userData = UserAccount::whereEmailAddress($data['email_address'])->first();

        if (!$userData || !Hash::check($data['password'], $userData->password)) {
            throw new AuthenticationException("Invalid Email or Password");
        }

        if($userData->verify_status == false){
            MessageService::createOTPCode($data['email_address'], OtpCodePurpose::ACCOUNT_CREATION->value);
            return [
                'user' => $userData,
                'verified' => false
            ];
        }

        // Clear IP-based search limit
        self::clearAnonymousSearchLimit();

         return $userData;
    }

    private static function clearAnonymousSearchLimit()
    {
        $request = request();
        if (!$request) return;

        $ipAddress = $request->ip();
        $identifier = md5($ipAddress);
        $cacheKey = "search_limit_{$identifier}";

        Cache::forget($cacheKey);
    }

   public static function updateUserPassword(array $data): bool
    {
        return UserAccount::whereEmailAddress($data['email_address'])->update(['password' => Hash::make($data['password'])]);
    }

    public static function validateOTP(array $data)
    {
        $otpRecord = OtpCode::whereCode($data)->first();

        if (!$otpRecord) {
            throw new Exception('Invalid OTP code', 404);
        }
        if (Carbon::parse($otpRecord->expires_at)->isPast()) {
            throw new Exception('OTP code has expired, kindly generate another OTP Code', 410);
        }
        return $otpRecord;
    }
}
