<?php

namespace App\Services\Utility;

use App\Enums\EmailVerificationPurpose;
use App\Enums\OtpCodePurpose;
use App\Models\OtpCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http; // Add this line
use Illuminate\Support\Facades\Log;

class MessageService
{
   public static function createOTPCode(string $emailAddress, string $purpose): void
{
    $messageContent = "";
    $otpCode = self::generateRandomToken(6);

    if ($purpose === OtpCodePurpose::ACCOUNT_CREATION->value) {
        $messageContent = " <p style='line-height: 30px; font-size: 16px; margin: 0;'>Welcome to <b>DN Warehouse</b>, your intelligent keyword research and domain discovery platform!</p>
                        </br>
                          <p style='line-height: 30px; font-size: 16px; margin: 0;'>We're excited to have you join our community of marketers, SEO professionals, and entrepreneurs
                          who rely on DN Warehouse for comprehensive keyword analysis and related search insights.</p></br>
                          <p style='line-height: 30px; font-size: 16px; margin: 0;'>To complete your registration, please use the OTP code below:</p>
                          <p class='bold' style='margin-top: 15px;color: #016FB9; font-weight: 700; line-height: 30px; font-size: 36px; letter-spacing: 10px; text-align: center;'>
                          {$otpCode}</p>
                           <p class='details' style='line-height: 30px; font-size: 16px; margin: 0;'>
                            This code is valid for the next 10 minutes. Please complete your registration promptly.</p>
                            <br />
                            <p style='line-height: 30px; font-size: 16px; margin: 0;'>
                              Need help? Our support team is always available at <a href='mailto:support@dnwhouse.com' style='color: #016FB9;'>support@dnwhouse.com</a></p>
                            <p style='line-height: 30px; font-size: 16px; margin: 0;'>Start discovering powerful keywords and grow your online presence today!</p>";

    } elseif ($purpose === OtpCodePurpose::FORGOT_PASSWORD->value) {
        $messageContent = " <p style='line-height: 30px; font-size: 16px; margin: 0;'>Forgot your password? Don't worry, we've got you covered!</p>
                            </br>
                              <p style='line-height: 30px; font-size: 16px; margin: 0;'>You recently requested to reset your password for your <b>DN Warehouse</b> account.</p>
                              <p style='line-height: 30px; font-size: 16px; margin: 0;'>Use the verification code below to reset your password:</p>
                              <p class='bold' style='margin-top: 15px;color: #016FB9; font-weight: 700; line-height: 30px; font-size: 36px; letter-spacing: 10px; text-align: center;'>
                              {$otpCode}</p>
                               <p class='details' style='line-height: 30px; font-size: 16px; margin: 0;'>
                                This code is valid for the next 10 minutes. If you did not request a password reset, please ignore this message or contact our support team immediately.</p>
                                <br />
                                <p style='line-height: 30px; font-size: 16px; margin: 0;'>
                                  To reset your password, click the link below:</p>
                                <p style='line-height: 30px; font-size: 16px; margin: 0; text-align: center;'>
                                  <a href='https://dnwhouse.com/forgot-password' style='color: #016FB9; font-weight: 700; text-decoration: none; padding: 10px 20px; background-color: #f0f8ff; border-radius: 5px; display: inline-block;'>Reset Password</a></p>
                                <br />
                                <p style='line-height: 30px; font-size: 16px; margin: 0;'>
                                  Need assistance? Our support team is here to help – reach out at <a href='mailto:support@dnwhouse.com' style='color: #016FB9;'>support@dnwhouse.com</a></p>
                                <p style='line-height: 30px; font-size: 16px; margin: 0;'>Continue discovering powerful keywords and growing your online presence with DN Warehouse.</p>";
    }

    self::mailMessage($emailAddress, $purpose, $messageContent);
    OtpCode::create(
        [
            'email_address' => $emailAddress,
            'purpose' => $purpose,
            'code' => $otpCode,
            'expires_at' => Carbon::now()->addMinutes(10)
        ]
    );
}

    public static function registrationMessage(array $data): void
    {
        $messageContent = " <p style='line-height: 30px; font-size: 16px; margin: 0;'>Welcome to <b>QuranLynk</b>! We are delighted to have you join our learning community.</p>
                    <br>
                    <p style='line-height: 30px; font-size: 16px; margin: 0;'>Your account has been successfully created. You can now log in using the credentials below:</p>
                    <p style='line-height: 30px; font-size: 16px; margin: 0;'><strong>Email:</strong> {$data['email_address']}</p>
                    <p style='line-height: 30px; font-size: 16px; margin: 0;'><strong>Password:</strong> {$data['password']}</p>
                    <br>
                    <p style='line-height: 30px; font-size: 16px; margin: 0;'>For security reasons, please change your password after logging in for the first time and ensure it remains confidential.</p>
                    <br>
                    <p style='line-height: 30px; font-size: 16px; margin: 0;'>If you did not create this account, please contact our support team immediately for assistance.</p>
                    <br>
                    <p style='line-height: 30px; font-size: 16px; margin: 0; text-align: center;'>
                        <a href='http://192.168.56.1:3000/login' style='color: #016FB9; font-weight: 700;'>Click here to log in</a>
                    </p>
                    <br>
                    <p style='line-height: 30px; font-size: 16px; margin: 0;'>We pray that your journey in learning the Quran is fruitful and beneficial.</p>
                    <br>
                    <p style='line-height: 30px; font-size: 16px; margin: 0;'>Barakallahu feek, and welcome to <b>QuranLynk</b>!</p>";

        self::mailMessage($data['email_address'], 'Account Creation', $messageContent);
    }

public static function mailMessage(string $emailAddress, string $subject, string $message, string $username = null): void
{

    try {

      $response = Http::post('http://localhost:3000/api/send', [
            'to' => $emailAddress,
            'subject' => $subject,
            'text' => $subject,
            'html' => $message,
            'from' => 'olaogunteslim@gmail.com',
        ]);


        if (!$response->successful()) {
            \Log::error('Email API failed: ' . $response->body());
        }

    } catch (\Exception $e) {
        \Log::error('Email sending failed: ' . $e->getMessage());
    }
}

    // In App\Services\Utility\MessageService.php
public static function createVerificationLink(string $emailAddress, string $purpose): void
{
    // Generate a unique token
    $token = self::generateRandomToken();
    $subject = "";

   if($purpose == EmailVerificationPurpose::EMAIL_VERIFICATION->value ){
      $frontendUrl = config('app.frontend_url');
    $verificationUrl = url("{$token}");
    $subject = "Dnwarehouse: Verify your email address";
    $messageContent = "<p style='line-height: 30px; font-size: 16px; margin: 0;'>Thank you for registering with <b>Dn Warehouse</b>!</p>
                      </br>
                      <p style='line-height: 30px; font-size: 16px; margin: 0;'>Please verify your email address by clicking the link below:</p>
                      <p style='text-align: center; margin: 20px 0;'>
                          <a href='{$verificationToken}' style='background-color: #016FB9; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Verify Email Address</a>
                      </p>
                      <p class='details' style='line-height: 30px; font-size: 16px; margin: 0;'>
                      This verification link will expire in 24 hours. If you didn't create an account, please ignore this email.</p>
                      <br />
                      <p style='line-height: 30px; font-size: 16px; margin: 0;'>
                        Need help? Our support team is always available to assist you.</p>
                      <p style='line-height: 30px; font-size: 16px; margin: 0;'>May your journey of learning the Quran be blessed.</p>";
   }
    if($purpose == EmailVerificationPurpose::FORGOT_PASSWORD->value ){
      $frontendUrl = config('app.frontend_url');
    $verificationUrl = url($frontendUrl."/reset-password/{$token}");
    $subject = "Dnwarehouse: Reset Password";
$messageContent = "
<p style='line-height: 30px; font-size: 16px; margin: 0;'>
  You requested a password reset for your <b>Dnwarehouse</b> domain management account.
</p>
<br/>
<p style='line-height: 30px; font-size: 16px; margin: 0;'>
  Click the button below to reset your password:
</p>
<p style='text-align: center; margin: 20px 0;'>
  <a href='{$verificationUrl}' style='background-color: #016FB9; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Reset Password</a>
</p>
<p style='line-height: 30px; font-size: 16px; margin: 0;'>
  <span style='color: #ff0000;'>This link expires in 24 hours.</span> Ignore this email if you didn't request a password reset.
</p>
<br/>
<p style='line-height: 30px; font-size: 16px; margin: 0;'>
  Need help? Contact <a href='mailto:support@dnwarehouse.com' style='color: #016FB9;'>support@dnwarehouse.com</a>.
</p>
";
   }
    self::mailMessage($emailAddress, $subject,$messageContent);

    OtpCode::create([
        'email_address' => $emailAddress,
        'purpose' => EmailVerificationPurpose::EMAIL_VERIFICATION->value,
        'code' => $token,
        'expires_at' => Carbon::now()->addHours(24)
    ]);
}

public static function generateRandomToken(int $length = 32): string
{
    $digits = '';

    for ($i = 0; $i < $length; $i++) {
        $digits .= random_int(0, 9); // cryptographically secure
    }

    return $digits;
}

}
