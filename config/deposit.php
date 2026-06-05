<?php

return [
    // Default M-Pesa Paybill number (Pochi La Biashara)
    'default_phone' => env('MPESA_DEPOSIT_NUMBER', '+254 111516744'),
    
    // Bonus amounts
    'registration_bonus' => env('REGISTRATION_BONUS', 60),
    'first_deposit_bonus' => env('FIRST_DEPOSIT_BONUS', 40),
    'consecutive_deposit_bonus' => env('CONSECUTIVE_DEPOSIT_BONUS', 20),
    
    // Minimum deposit
    'min_deposit' => env('MIN_DEPOSIT', 10),
    'max_deposit' => env('MAX_DEPOSIT', 500000),
    
    // Deposit expiry (hours)
    'request_expiry_hours' => env('DEPOSIT_REQUEST_EXPIRY', 48),
];
