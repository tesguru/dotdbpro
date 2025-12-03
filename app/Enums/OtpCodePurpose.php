<?php

namespace App\Enums;

enum OtpCodePurpose: string
{
    case FORGOT_PASSWORD = "FORGOT-PASSWORD";
   
    case ACCOUNT_CREATION = "ACCOUNT_CREATION";
}
