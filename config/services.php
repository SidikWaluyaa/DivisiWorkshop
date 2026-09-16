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

    'whatsapp' => [
        'support_number' => env('WHATSAPP_SUPPORT_NUMBER', '628123456789'),
    ],

    'finlog' => [
        'base_url' => env('FINLOG_BASE_URL', 'https://finlog.shoeworkshop.id/api/v1'),
        'bearer_token' => env('FINLOG_BEARER_TOKEN', 'mock_token_finlog_secret_2026'),
        'webhook_secret' => env('FINLOG_WEBHOOK_SECRET', 'secret_finlog_webhook_key_2026'),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'api_keys' => array_values(array_filter(array_map('trim', explode(',', env('GEMINI_API_KEYS', env('GEMINI_API_KEY', '')))))),
        'model' => env('GEMINI_MODEL', 'gemini-3.5-flash-lite'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
    ],

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL', 'qwen/qwen3.8-27b'),
        'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
    ],

];
