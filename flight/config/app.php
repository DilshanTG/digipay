<?php

return [
    'name' => $_ENV['APP_NAME'] ?? 'DigiPay',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    'timezone' => 'Asia/Colombo',

    // Admin
    'admin_password' => $_ENV['ADMIN_PASSWORD'] ?? 'admin123',

    // PayHere
    'payhere' => [
        'merchant_id' => $_ENV['PAYHERE_MERCHANT_ID'] ?? '',
        'merchant_secret' => $_ENV['PAYHERE_MERCHANT_SECRET'] ?? '',
        'mode' => $_ENV['PAYHERE_MODE'] ?? 'sandbox', // sandbox or live
        'app_id' => $_ENV['PAYHERE_APP_ID'] ?? '',
        'app_secret' => $_ENV['PAYHERE_APP_SECRET'] ?? '',
    ],

    // SMS
    'sms' => [
        'api_key' => $_ENV['SMS_API_KEY'] ?? '',
        'sender_id' => $_ENV['SMS_SENDER_ID'] ?? 'DigiPay',
    ],

    // Supabase
    'supabase' => [
        'url' => $_ENV['SUPABASE_URL'] ?? '',
        'key' => $_ENV['SUPABASE_KEY'] ?? '',
    ],
];
