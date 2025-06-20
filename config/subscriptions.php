<?php

return [
    'enable_billing' => env('ENABLE_BILLING', false),
    'user_seats' => [
        'monthly_price_id' => env('STRIPE_SUBSCRIPTION_USER_MONTHLY_PRICE_ID', 'price_1RVYEYPISqZjp4j7hRNH6hGQ')   
    ],
    'startup' => [
        'monthly_price_id' => env('STRIPE_SUBSCRIPTION_STARTUP_MONTHLY_PRICE_ID', 'price_startup_default'),
        'weekly_load_limit' => env('STARTUP_WEEKLY_LOAD_LIMIT', 10),
    ]
];

