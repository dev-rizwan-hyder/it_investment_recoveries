<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
    ],

    'square' => [
        'environment' => env('SQUARE_ENVIRONMENT', 'sandbox'),
        'token' => env('SQUARE_ACCESS_TOKEN'),
        'location_id' => env('SQUARE_LOCATION_ID'),
        'app_id' => env('SQUARE_APPLICATION_ID'),
    ],

    'ebay' => [
        'mode' => env('EBAY_ENV', 'production'),
        'client_id' => env('EBAY_CLIENT_ID', env('CLIENT_ID')),
        'client_secret' => env('EBAY_CLIENT_SECRET', env('CLIENT_SECRET')),
        'ru_name' => env('EBAY_RU_NAME', env('RU_NAME')),
        'seller_username' => env('EBAY_SELLER_USERNAME', 'systematictrade'),
        'refresh_token' => env('EBAY_REFRESH_TOKEN', env('REFRESH_TOKEN')),
        'verification_token' => env('EBAY_VERIFICATION_TOKEN'),
        'oauth_url' => env('EBAY_OAUTH_URL'),
        'api_url' => env('EBAY_API_URL'),
    ],

];
