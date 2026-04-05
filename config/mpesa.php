<?php

return [
    /*
    |--------------------------------------------------------------------------
    | M-Pesa Environment
    |--------------------------------------------------------------------------
    */
    'environment' => env('MPESA_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Consumer Credentials (provided by Safaricom)
    |--------------------------------------------------------------------------
    */
    'consumer_key'    => env('MPESA_CONSUMER_KEY', 'your_consumer_key'),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET', 'your_consumer_secret'),

    /*
    |--------------------------------------------------------------------------
    | Business Short Code
    |--------------------------------------------------------------------------
    */
    'shortcode' => env('MPESA_SHORTCODE', '174379'), // Sandbox shortcode

    /*
    |--------------------------------------------------------------------------
    | Passkey (for STK Push)
    |--------------------------------------------------------------------------
    */
    'passkey' => env('MPESA_PASSKEY', 'your_passkey'),

    /*
    |--------------------------------------------------------------------------
    | Initiator Name & Password (for B2C)
    |--------------------------------------------------------------------------
    */
    'initiator_name'      => env('MPESA_INITIATOR_NAME', 'testapi'),
    'initiator_password'  => env('MPESA_INITIATOR_PASSWORD', 'your_initiator_password'),

    /*
    |--------------------------------------------------------------------------
    | API URLs
    |--------------------------------------------------------------------------
    */
    'urls' => [
        'sandbox' => [
            'oauth'       => 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials',
            'stk_push'    => 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest',
            'b2c'         => 'https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest',
            'transaction_status' => 'https://sandbox.safaricom.co.ke/mpesa/transactionstatus/v1/query',
        ],
        'production' => [
            'oauth'       => 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials',
            'stk_push'    => 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest',
            'b2c'         => 'https://api.safaricom.co.ke/mpesa/b2c/v1/paymentrequest',
            'transaction_status' => 'https://api.safaricom.co.ke/mpesa/transactionstatus/v1/query',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Callback URLs (must be defined in .env)
    |--------------------------------------------------------------------------
    */
    'callback_url' => [
        'stk'   => env('MPESA_STK_CALLBACK_URL', 'https://your-domain.com/api/mpesa/callback'),
        'b2c'   => env('MPESA_B2C_CALLBACK_URL', 'https://your-domain.com/api/mpesa/b2c-callback'),
    ],
];
