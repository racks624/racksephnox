<?php

use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/balance', [WalletController::class, 'balance']);
    Route::get('/transactions', [WalletController::class, 'transactions']);
    Route::post('/deposit', [WalletController::class, 'deposit']);
    Route::post('/withdraw', [WalletController::class, 'withdraw']);
});
