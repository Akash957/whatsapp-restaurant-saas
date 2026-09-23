<?php

return [

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

    'razorpay' => [
        'key' => env('RAZORPAY_KEY'),
        'secret' => env('RAZORPAY_SECRET'),
        'mode' => env('RAZORPAY_MODE', 'test'),
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
    ],

    'whatsapp' => [
        'api_version' => env('WHATSAPP_API_VERSION', 'v23.0'),
        'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
        'webhook_verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),
        'app_secret' => env('WHATSAPP_APP_SECRET'),
    ],

    'ai' => [
        'provider' => env('AI_PROVIDER', 'openai'),
        'api_key' => env('AI_API_KEY'),
        'model' => env('AI_MODEL', 'gpt-4o-mini'),
        'base_url' => env('AI_BASE_URL'),
    ],
];