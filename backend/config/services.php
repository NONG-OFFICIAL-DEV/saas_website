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

    // Onboarding orchestrator targets — each product owns its own tenant/
    // auth/billing entirely; the platform only ever calls their existing
    // public registration endpoints, never touches their databases.
    'studio' => [
        'base_url' => env('STUDIO_API_BASE_URL', 'https://photo-studio.nexstacktech.com'),
        'login_url' => env('STUDIO_LOGIN_URL', 'https://photo-studio.nexstacktech.com/login'),
    ],

    'smart_store' => [
        'base_url' => env('SMART_STORE_API_BASE_URL', 'https://admin.nexstacktech.com'),
        'login_url' => env('SMART_STORE_LOGIN_URL', 'https://admin.nexstacktech.com/login'),
    ],

];
