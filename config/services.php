<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file stores credentials for third-party services like Mailgun,
    | Postmark, AWS, and more. This provides a conventional location
    | for packages to locate service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'api_key' => env('RESEND_API_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'               => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'dodo_payment' => [
        'base_url' => env('DODO_MODE') === 'live'
            ? env('DODO_BASE_URL_LIVE')
            : env('DODO_BASE_URL_TEST'),

        'api_key' => env('DODO_MODE') === 'live'
            ? env('DODO_API_KEY_LIVE')
            : env('DODO_API_KEY_TEST'),
    ],

];
