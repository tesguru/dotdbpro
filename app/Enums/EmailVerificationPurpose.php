<?php

namespace App\Enums;

enum EmailVerificationPurpose: string
{
    case FORGOT_PASSWORD = "FORGOT-PASSWORD";
    case EMAIL_VERIFICATION = 'email_verification';  
    case ACCOUNT_CREATION = "ACCOUNT_CREATION";
}
