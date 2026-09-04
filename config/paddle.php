<?php

return [
    'environment' => env('PADDLE_ENVIRONMENT', 'sandbox'),

    'api_key' => env('PADDLE_API_KEY'),

    'client_token' => env('PADDLE_CLIENT_TOKEN'),

    'test_price_id' => env('PADDLE_TEST_PRICE_ID'),

    'webhook_secret' => env('PADDLE_WEBHOOK_SECRET'),

    'verify_ssl' => env('PADDLE_VERIFY_SSL', true),
];
