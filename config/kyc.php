<?php

return [
    'provider' => env('KYC_PROVIDER', 'identitypass'),
    'identitypass' => [
        'base_url' => 'https://api.identitypass.com',
        'api_key'  => env('IDENTITYPASS_API_KEY'),
        'secret'   => env('IDENTITYPASS_SECRET'),
    ],
    'storage' => [
        'disk' => 'public',
        'folder' => 'kyc-documents',
    ],
    'levels' => [
        'basic' => ['phone', 'email'],
        'tier1' => ['id_card', 'selfie'],
        'tier2' => ['proof_of_address', 'bank_statement'],
    ],
];
