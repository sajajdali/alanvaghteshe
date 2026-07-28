<?php

return [
    'base_url' => env('WHATSAPP_BASE_URL', 'https://t00r.ir/api'),
    'api_key'  => env('WHATSAPP_API_KEY'),
    'default_session' => env('WHATSAPP_DEFAULT_SESSION', 'default'),
    'timeout' => (int) env('WHATSAPP_TIMEOUT', 15),
    'retry' => [
        'times' => (int) env('WHATSAPP_RETRY_TIMES', 3),
        'sleep' => (int) env('WHATSAPP_RETRY_SLEEP', 200), // میلی‌ثانیه
    ],
];
