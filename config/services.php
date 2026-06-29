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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

     'google' => [
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY', ''),
    ],


    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'currency' => env('STRIPE_CURRENCY', 'USD'),
    ],

    'payment' => [
        'gateway' => env('PAYMENT_GATEWAY', 'stripe'),
        'test_mode' => env('PAYMENT_TEST_MODE', true),
        'currency' => env('PAYMENT_CURRENCY', env('STRIPE_CURRENCY', env('CURRENCY', 'USD'))),
        'due_days' => env('PAYMENT_DUE_DAYS', 7),
        'required_before_pickup' => env('PAYMENT_REQUIRED_BEFORE_PICKUP', true),
        'admin_email' => env('PAYMENT_ADMIN_EMAIL', 'info@neoprolab.com'),
    ],

];
