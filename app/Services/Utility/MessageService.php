<?php

namespace App\Services\Utility;

use App\Enums\EmailVerificationPurpose;
use App\Enums\OtpCodePurpose;
use App\Models\OtpCode;
use Carbon\Carbon;
use Resend;
use Illuminate\Support\Facades\Log;

class MessageService
{
    public static function createOTPCode(string $emailAddress, string $purpose): void
    {
        $messageContent = "";
        $otpCode = self::generateRandomToken(6);

        if ($purpose === OtpCodePurpose::ACCOUNT_CREATION->value) {
            $subject = "Dnwhouse: Verify Your Account";
            $messageContent = " <p style='line-height: 30px; font-size: 16px; margin: 0;'>Welcome to <b>Dnwhouse</b>, your intelligent keyword research and domain discovery platform!</p>
                        </br>
                          <p style='line-height: 30px; font-size: 16px; margin: 0;'>We're excited to have you join our community of marketers, SEO professionals, and entrepreneurs
                          who rely on Dnwhouse for comprehensive keyword analysis and related search insights.</p></br>
                          <p style='line-height: 30px; font-size: 16px; margin: 0;'><b>Your One-Time Password (OTP) for account verification:</b></p>
                          <p class='bold' style='margin-top: 15px;color: #016FB9; font-weight: 700; line-height: 30px; font-size: 36px; letter-spacing: 10px; text-align: center;'>
                          {$otpCode}</p>
                           <p class='details' style='line-height: 30px; font-size: 16px; margin: 0; text-align: center;'>
                            <b>This OTP code expires in 10 minutes.</b> Please complete your registration promptly.</p>
                            <br />
                            <p style='line-height: 30px; font-size: 16px; margin: 0;'>
                              Need help? Our support team is always available at <a href='mailto:support@dnwhouse.com' style='color: #016FB9;'>support@dnwhouse.com</a></p>
                            <p style='line-height: 30px; font-size: 16px; margin: 0;'>Start discovering powerful keywords and grow your online presence today!</p>";

        } elseif ($purpose === OtpCodePurpose::FORGOT_PASSWORD->value) {
            $subject = "Dnwhouse: Password Reset Request";
            $messageContent = " <p style='line-height: 30px; font-size: 16px; margin: 0;'>Forgot your password? Don't worry, we've got you covered!</p>
                            </br>
                              <p style='line-height: 30px; font-size: 16px; margin: 0;'>You recently requested to reset your password for your <b>Dnwhouse</b> account.</p>
                              <p style='line-height: 30px; font-size: 16px; margin: 0;'><b>Your One-Time Password (OTP) for password reset:</b></p>
                              <p class='bold' style='margin-top: 15px;color: #016FB9; font-weight: 700; line-height: 30px; font-size: 36px; letter-spacing: 10px; text-align: center;'>
                              {$otpCode}</p>
                               <p class='details' style='line-height: 30px; font-size: 16px; margin: 0; text-align: center;'>
                                <b>This OTP code expires in 10 minutes.</b> If you did not request a password reset, please ignore this message or contact our support team immediately.</p>
                                <br />
                                <p style='line-height: 30px; font-size: 16px; margin: 0;'>
                                  To reset your password, enter the OTP code on the verification page or click the link below:</p>
                                <p style='line-height: 30px; font-size: 16px; margin: 0; text-align: center;'>
                                  <a href='https://dnwhouse.com/reset-password' style='color: #016FB9; font-weight: 700; text-decoration: none; padding: 10px 20px; background-color: #f0f8ff; border-radius: 5px; display: inline-block;'>Reset Password</a></p>
                                <br />
                                <p style='line-height: 30px; font-size: 16px; margin: 0;'>
                                  Need assistance? Our support team is here to help – reach out at <a href='mailto:support@dnwhouse.com' style='color: #016FB9;'>support@dnwhouse.com</a></p>
                                <p style='line-height: 30px; font-size: 16px; margin: 0;'>Continue discovering powerful keywords and growing your online presence with Dnwhouse.</p>";
        }

        self::mailMessage($emailAddress, $subject, $messageContent);
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
        $messageContent = " <p style='line-height: 30px; font-size: 16px; margin: 0;'>Welcome to <b>Dnwhouse</b>! We are delighted to have you join our platform.</p>
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
                        <a href='https://dnwhouse.com/login' style='color: #016FB9; font-weight: 700;'>Click here to log in</a>
                    </p>
                    <br>
                    <p style='line-height: 30px; font-size: 16px; margin: 0;'>We look forward to helping you grow your online presence!</p>
                    <br>
                    <p style='line-height: 30px; font-size: 16px; margin: 0;'>Welcome to <b>Dnwhouse</b>!</p>";

        self::mailMessage($data['email_address'], 'Dnwhouse: Account Creation', $messageContent);
    }

    public static function mailMessage(string $emailAddress, string $subject, string $message, string $username = null): void
    {
        try {
            $resend = Resend::client(config('services.resend.api_key'));

            $resend->emails->send([
                'from' => 'Dnwhouse <noreply@dnwhouse.com>',
                'to' => [$emailAddress],
                'subject' => $subject,
                'html' => self::wrapEmailTemplate($message),
            ]);

            Log::info('Email sent successfully to: ' . $emailAddress);

        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function createVerificationLink(string $emailAddress, string $purpose): void
    {
        $token = self::generateRandomToken();
        $subject = "";
        $messageContent = "";

        if ($purpose == EmailVerificationPurpose::EMAIL_VERIFICATION->value) {
            $frontendUrl = config('app.frontend_url');
            $verificationUrl = $frontendUrl . "/verify-email/{$token}";
            $subject = "Dnwhouse: Verify Your Email Address";
            $messageContent = "<p style='line-height: 30px; font-size: 16px; margin: 0;'>Thank you for registering with <b>Dnwhouse</b>!</p>
                          </br>
                          <p style='line-height: 30px; font-size: 16px; margin: 0;'>Please verify your email address by clicking the link below:</p>
                          <p style='text-align: center; margin: 20px 0;'>
                              <a href='{$verificationUrl}' style='background-color: #016FB9; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Verify Email Address</a>
                          </p>
                          <p class='details' style='line-height: 30px; font-size: 16px; margin: 0;'>
                          This verification link will expire in 24 hours. If you didn't create an account, please ignore this email.</p>
                          <br />
                          <p style='line-height: 30px; font-size: 16px; margin: 0;'>
                            Need help? Our support team is always available at <a href='mailto:support@dnwhouse.com' style='color: #016FB9;'>support@dnwhouse.com</a>.</p>";
        }

        if ($purpose == EmailVerificationPurpose::FORGOT_PASSWORD->value) {
            $frontendUrl = config('app.frontend_url');
            $verificationUrl = $frontendUrl . "/reset-password/{$token}";
            $subject = "DNWHouse: Reset Password";
            $messageContent = "
            <p style='line-height: 30px; font-size: 16px; margin: 0;'>
              You requested a password reset for your <b>DNWHouse</b> account.
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
              Need help? Contact <a href='mailto:support@dnwhouse.com' style='color: #016FB9;'>support@dnwhouse.com</a>.
            </p>
            ";
        }

        self::mailMessage($emailAddress, $subject, $messageContent);

        OtpCode::create([
            'email_address' => $emailAddress,
            'purpose' => $purpose,
            'code' => $token,
            'expires_at' => Carbon::now()->addHours(24)
        ]);
    }

    public static function generateRandomToken(int $length = 32): string
    {
        $digits = '';

        for ($i = 0; $i < $length; $i++) {
            $digits .= random_int(0, 9);
        }

        return $digits;
    }

    /**
     * Wrap email content in a professional template
     */
    private static function wrapEmailTemplate(string $content): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        </head>
        <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;'>
            <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f4f4f4; padding: 20px;'>
                <tr>
                    <td align='center'>
                        <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                            <!-- Header -->
                            <tr>
                                <td style='background-color: #016FB9; padding: 30px; text-align: center;'>
                                    <h1 style='color: #ffffff; margin: 0; font-size: 28px;'>DNWHouse</h1>
                                </td>
                            </tr>
                            <!-- Content -->
                            <tr>
                                <td style='padding: 40px 30px;'>
                                    {$content}
                                </td>
                            </tr>
                            <!-- Footer -->
                            <tr>
                                <td style='background-color: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #e9ecef;'>
                                    <p style='margin: 0; font-size: 14px; color: #6c757d;'>
                                        © " . date('Y') . " DNWHouse. All rights reserved.
                                    </p>
                                    <p style='margin: 10px 0 0 0; font-size: 12px; color: #6c757d;'>
                                        <a href='https://dnwhouse.com' style='color: #016FB9; text-decoration: none;'>Visit our website</a> |
                                        <a href='mailto:support@dnwhouse.com' style='color: #016FB9; text-decoration: none;'>Contact Support</a>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";
    }
}
