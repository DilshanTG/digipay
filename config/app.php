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
        'mode' => $_ENV['PAYHERE_MODE'] ?? 'live',
    ],

    // SMS (SMSAPI.LK)
    'sms' => [
        'api_token' => $_ENV['SMS_API_TOKEN'] ?? '',
        'sender_id' => $_ENV['SMS_SENDER_ID'] ?? 'DIGIMART',
        'enabled' => filter_var($_ENV['SMS_ENABLED'] ?? true, FILTER_VALIDATE_BOOLEAN),
    ],

    // WhatsApp (ManyContacts)
    'whatsapp' => [
        'api_key' => $_ENV['WHATSAPP_API_KEY'] ?? '',
        'admin_number' => $_ENV['WHATSAPP_ADMIN_NUMBER'] ?? '',
    ],

    // Zoho ZeptoMail
    'zoho' => [
        'api_key' => $_ENV['ZOHO_API_KEY'] ?? '',
    ],

    // Supabase
    'supabase' => [
        'url' => $_ENV['SUPABASE_URL'] ?? '',
        'key' => $_ENV['SUPABASE_KEY'] ?? '',
    ],
];
