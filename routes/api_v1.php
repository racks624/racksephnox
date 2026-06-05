<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/register', [App\Http\Controllers\Api\V1\AuthController::class, 'register']);
    Route::post('/login', [AppHttpControllersApiV1AuthController::class, 'login'])->middleware('throttle:api_auth');
    Route::post('/login', [App\Http\Controllers\Api\V1\AuthController::class, 'login']);

    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [App\Http\Controllers\Api\V1\AuthController::class, 'logout']);
        Route::get('/user', [App\Http\Controllers\Api\V1\AuthController::class, 'user']);

        // Wallet
        Route::get('/wallet/balance', [App\Http\Controllers\Api\V1\WalletController::class, 'balance']);
        Route::get('/wallet/transactions', [App\Http\Controllers\Api\V1\WalletController::class, 'transactions']);

        // Investments
        Route::get('/investments/plans', [App\Http\Controllers\Api\V1\InvestmentController::class, 'plans']);
        Route::post('/investments/invest', [App\Http\Controllers\Api\V1\InvestmentController::class, 'invest']);
        Route::get('/investments/my', [App\Http\Controllers\Api\V1\InvestmentController::class, 'myInvestments']);

        // Trading
        Route::get('/trading/balance', [App\Http\Controllers\Api\V1\TradingController::class, 'balance']);
        Route::get('/trading/price', [App\Http\Controllers\Api\V1\TradingController::class, 'price']);
        Route::post('/trading/buy', [App\Http\Controllers\Api\V1\TradingController::class, 'buy']);
        Route::post('/trading/sell', [App\Http\Controllers\Api\V1\TradingController::class, 'sell']);
        Route::get('/trading/orders', [App\Http\Controllers\Api\V1\TradingController::class, 'orders']);

        // Deposit
        Route::get('/deposit/pochi-number', [App\Http\Controllers\Api\V1\DepositController::class, 'getPochiNumber']);
        Route::post('/deposit/submit', [App\Http\Controllers\Api\V1\DepositController::class, 'submitRequest']);
        Route::get('/deposit/history', [App\Http\Controllers\Api\V1\DepositController::class, 'history']);

        // Withdrawal
        Route::post('/withdrawal/submit', [App\Http\Controllers\Api\V1\WithdrawalController::class, 'submitRequest']);
        Route::get('/withdrawal/history', [App\Http\Controllers\Api\V1\WithdrawalController::class, 'history']);
    });
});
