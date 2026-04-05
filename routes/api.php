<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InvestmentController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\MpesaController;
use App\Http\Controllers\Api\KycController;
use App\Http\Controllers\Api\TradingController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\WithdrawalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Version 1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/mpesa/callback', [MpesaController::class, 'callback'])->name('api.mpesa.callback');

    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // Authentication
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Wallet
        Route::get('/wallet/balance', [WalletController::class, 'balance']);
        Route::get('/wallet/transactions', [WalletController::class, 'transactions']);

        // Investments
        Route::get('/investment-plans', [InvestmentController::class, 'plans']);
        Route::get('/investments', [InvestmentController::class, 'index']);
        Route::post('/investments', [InvestmentController::class, 'store']);
        Route::get('/investments/{id}', [InvestmentController::class, 'show']);
        Route::get('/plan/{plan}/daily-profit', [InvestmentController::class, 'dailyProfit']);

        // Trading
        Route::get('/trading/balance', [TradingController::class, 'balance']);
        Route::get('/trading/price', [TradingController::class, 'price']);
        Route::post('/trading/buy', [TradingController::class, 'buy']);
        Route::post('/trading/sell', [TradingController::class, 'sell']);
        Route::get('/trading/orders', [TradingController::class, 'orders']);

        // KYC
        Route::get('/kyc/status', [KycController::class, 'status']);
        Route::post('/kyc/upload', [KycController::class, 'upload']);
        Route::post('/kyc/verify-id', [KycController::class, 'verifyId']);

        // Deposit
        Route::get('/deposit/pochi-number', [DepositController::class, 'getPochiNumber']);
        Route::post('/deposit/submit', [DepositController::class, 'submitRequest']);
        Route::get('/deposit/history', [DepositController::class, 'history']);

        // Withdrawal
        Route::post('/withdrawal/submit', [WithdrawalController::class, 'submitRequest']);
        Route::get('/withdrawal/history', [WithdrawalController::class, 'history']);
    });
});

// Notifications
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications/unread-count', function (Request $request) {
        return response()->json(['count' => $request->user()->unreadNotifications->count()]);
    });
});
