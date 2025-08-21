<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],
// config/services.php

    // ...

    // ...
// config/services.php
'whatsapp' => [
    'token'       => env('WHATSAPP_ACCESS_TOKEN', ''),
    'version'     => env('WHATSAPP_API_VERSION', 'v21.0'),
    'phone_id'    => env('WHATSAPP_PHONE_NUMBER_ID', ''),
    'phone_e164'  => env('WHATSAPP_PHONE_NUMBER', ''), // en E.164, ya lo tienes 5215665864626
    'waba_id'     => env('WHATSAPP_BUSINESS_ACCOUNT_ID', ''),
    'verify_token'=> env('WHATSAPP_VERIFY_TOKEN', ''), // 👈 nuevo
],







    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
