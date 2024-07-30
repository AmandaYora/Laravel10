<?php

return [

    'telegram' => [
        'url' => env('TELEGRAM_URL', 'https://api.telegram.org/bot'),
        'token' => env('TELEGRAM_TOKEN', 'your-telegram-bot-token-here'),
    ],

    'smtp' => [
        'host' => env('SMTP_HOST', 'smtp.example.com'),
        'port' => env('SMTP_PORT', 587),
        'username' => env('SMTP_USERNAME', 'your_username'),
        'password' => env('SMTP_PASSWORD', 'your_password'),
        'encryption' => env('SMTP_ENCRYPTION', 'tls'),
    ],

    'otp' => ['telegram', 'email']
];
