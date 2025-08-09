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

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    // config/services.php
    'asist_sms' => [
        'wsdl_url' => env('ASIST_WSDL_URL', 'https://webservice.asistiletisim.com.tr/SmsProxy.asmx?WSDL'),
        'usercode' => env('ASIST_USERCODE'),
        'username' => env('ASIST_USERNAME'),
        'password' => env('ASIST_PASSWORD'),
        'account_id_otp' => env('ASIST_ACCOUNT_ID_OTP'),
        'originator' => env('ASIST_ORIGINATOR'),
    ],
];
