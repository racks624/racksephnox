<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InvestmentController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\MpesaController;
use App\Http\Controllers\Api\KycController;
use App\Http\Controllers\Api\TradingController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\WithdrawalController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\Api\CryptoController;
use App\Http\Controllers\Api\MachineController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Version 1)
|--------------------------------------------------------------------------
| Base URL: /api/v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    
    // ==============================================
    // PUBLIC ROUTES (No authentication required)
    // ==============================================
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/mpesa/callback', [MpesaController::class, 'callback'])->name('api.mpesa.callback');
    Route::get('/crypto/prices', [CryptoController::class, 'prices'])->name('api.crypto.prices');
    Route::get('/investment-plans', [InvestmentController::class, 'plans']);
    Route::get('/machine/list', [MachineController::class, 'list']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // ==============================================
    // PROTECTED ROUTES (Requires authentication)
    // ==============================================
    Route::middleware('auth:sanctum')->group(function () {
        
        // ------------------------------------------
        // AUTHENTICATION & USER MANAGEMENT
        // ------------------------------------------
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/user/profile', [AuthController::class, 'updateProfile']);
        Route::put('/user/password', [AuthController::class, 'updatePassword']);
        Route::delete('/user/account', [AuthController::class, 'deleteAccount']);
        
        // ------------------------------------------
        // WALLET MANAGEMENT
        // ------------------------------------------
        Route::prefix('wallet')->group(function () {
            Route::get('/balance', [WalletController::class, 'balance']);
            Route::get('/transactions', [WalletController::class, 'transactions']);
            Route::post('/transfer', [WalletController::class, 'transfer']);
            Route::get('/summary', [WalletController::class, 'summary']);
        });
        
        // ------------------------------------------
        // INVESTMENTS
        // ------------------------------------------
        Route::prefix('investments')->group(function () {
            Route::get('/', [InvestmentController::class, 'index']);
            Route::post('/', [InvestmentController::class, 'store']);
            Route::get('/{id}', [InvestmentController::class, 'show']);
            Route::post('/{id}/withdraw', [InvestmentController::class, 'earlyWithdraw']);
            Route::get('/plan/{plan}/daily-profit', [InvestmentController::class, 'dailyProfit']);
            Route::get('/stats', [InvestmentController::class, 'stats']);
        });
        
        // ------------------------------------------
        // TRADING
        // ------------------------------------------
        Route::prefix('trading')->group(function () {
            Route::get('/balance', [TradingController::class, 'balance']);
            Route::get('/price', [TradingController::class, 'price']);
            Route::post('/buy', [TradingController::class, 'buy']);
            Route::post('/sell', [TradingController::class, 'sell']);
            Route::get('/orders', [TradingController::class, 'orders']);
            Route::get('/history', [TradingController::class, 'history']);
            Route::post('/order/{id}/cancel', [TradingController::class, 'cancelOrder']);
        });
        
        // ------------------------------------------
        // MACHINES (Investment Plans)
        // ------------------------------------------
        Route::prefix('machines')->group(function () {
            Route::get('/', [MachineController::class, 'index']);
            Route::get('/{code}', [MachineController::class, 'show']);
            Route::post('/{machine}/invest', [MachineController::class, 'invest']);
            Route::get('/{investment}/status', [MachineController::class, 'status']);
        });
        
        // ------------------------------------------
        // KYC (Know Your Customer)
        // ------------------------------------------
        Route::prefix('kyc')->group(function () {
            Route::get('/status', [KycController::class, 'status']);
            Route::post('/upload', [KycController::class, 'upload']);
            Route::post('/verify-id', [KycController::class, 'verifyId']);
            Route::get('/documents', [KycController::class, 'documents']);
            Route::delete('/document/{id}', [KycController::class, 'deleteDocument']);
        });
        
        // ------------------------------------------
        // DEPOSITS
        // ------------------------------------------
        Route::prefix('deposit')->group(function () {
            Route::get('/pochi-number', [DepositController::class, 'getPochiNumber']);
            Route::post('/submit', [DepositController::class, 'submitRequest']);
            Route::get('/history', [DepositController::class, 'history']);
            Route::get('/status/{id}', [DepositController::class, 'status']);
            Route::post('/verify', [DepositController::class, 'verifyPayment']);
        });
        
        // ------------------------------------------
        // WITHDRAWALS
        // ------------------------------------------
        Route::prefix('withdrawal')->group(function () {
            Route::post('/submit', [WithdrawalController::class, 'submitRequest']);
            Route::get('/history', [WithdrawalController::class, 'history']);
            Route::get('/status/{id}', [WithdrawalController::class, 'status']);
            Route::post('/bank-account', [WithdrawalController::class, 'addBankAccount']);
            Route::delete('/bank-account/{id}', [WithdrawalController::class, 'removeBankAccount']);
        });
        
        // ------------------------------------------
        // TRANSACTIONS
        // ------------------------------------------
        Route::prefix('transactions')->group(function () {
            Route::get('/', [TransactionController::class, 'index']);
            Route::get('/export', [TransactionController::class, 'export']);
            Route::get('/summary', [TransactionController::class, 'summary']);
            Route::get('/types', [TransactionController::class, 'types']);
        });
        
        // ------------------------------------------
        // REFERRALS
        // ------------------------------------------
        Route::prefix('referrals')->group(function () {
            Route::get('/stats', [ReferralController::class, 'stats']);
            Route::get('/list', [ReferralController::class, 'list']);
            Route::get('/bonuses', [ReferralController::class, 'bonuses']);
            Route::get('/link', [ReferralController::class, 'getLink']);
        });
        
        // ------------------------------------------
        // CRYPTO
        // ------------------------------------------
        Route::prefix('crypto')->group(function () {
            Route::get('/prices', [CryptoController::class, 'prices']);
            Route::get('/history', [CryptoController::class, 'history']);
            Route::get('/market', [CryptoController::class, 'marketData']);
        });
        
        // ------------------------------------------
        // NOTIFICATIONS
        // ------------------------------------------
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
            Route::post('/mark-all-read', [NotificationController::class, 'markAllRead']);
            Route::delete('/{id}', [NotificationController::class, 'destroy']);
            Route::delete('/', [NotificationController::class, 'destroyAll']);
            Route::get('/preferences', [NotificationController::class, 'preferences']);
            Route::post('/preferences', [NotificationController::class, 'updatePreferences']);
        });
        
        // ------------------------------------------
        // DASHBOARD STATS
        // ------------------------------------------
        Route::get('/dashboard/stats', function (Request $request) {
            $user = $request->user();
            return response()->json([
                'wallet_balance' => $user->wallet->balance ?? 0,
                'total_invested' => $user->investments()->sum('amount'),
                'active_investments' => $user->investments()->where('status', 'active')->count(),
                'total_profit' => $user->transactions()->where('type', 'interest')->sum('amount'),
                'total_referrals' => $user->referrals()->count(),
                'total_bonus' => $user->transactions()->where('type', 'referral_bonus')->sum('amount'),
            ]);
        });
        
        // ------------------------------------------
        // REFERRAL STATS (Quick endpoint)
        // ------------------------------------------
        Route::get('/referral-stats', function (Request $request) {
            $user = $request->user();
            return response()->json([
                'total_referrals' => $user->referrals()->count(),
                'total_bonus' => $user->transactions()->where('type', 'referral_bonus')->sum('amount'),
                'referral_link' => url('/refer/' . $user->referral_code),
            ]);
        });
        
        // ------------------------------------------
        // CRYPTO PRICES (Quick endpoint)
        // ------------------------------------------
        Route::get('/crypto-prices', function () {
            $prices = \App\Models\CryptoPrice::latest()->take(5)->get();
            return response()->json($prices);
        });
    });
});

// ==============================================
// HEALTH CHECK ENDPOINT
// ==============================================
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now(),
        'app_name' => config('app.name'),
        'version' => '1.0.0'
    ]);
});

// ==============================================
// API VERSION INFO
// ==============================================
Route::get('/v1/info', function () {
    return response()->json([
        'version' => '1.0.0',
        'name' => 'Racksephnox Crypto API',
        'description' => 'Divine Golden Spirit API',
        'endpoints' => [
            'auth' => '/v1/login, /v1/register, /v1/logout',
            'wallet' => '/v1/wallet/*',
            'investments' => '/v1/investments/*',
            'trading' => '/v1/trading/*',
            'kyc' => '/v1/kyc/*',
            'deposit' => '/v1/deposit/*',
            'withdrawal' => '/v1/withdrawal/*',
        ]
    ]);
});

// ==============================================
// ADDITIONAL API ROUTES (for dashboard widgets)
// ==============================================

Route::middleware('auth:sanctum')->group(function () {

    // Wallet balance (used by dashboard refresh)
    Route::get('/wallet', function (Request $request) {
        return response()->json([
            'balance' => $request->user()->wallet->balance ?? 0,
            'currency' => 'KES'
        ]);
    })->name('api.wallet');

    // Referral stats (used by referralWidget)
    Route::get('/referral-stats', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'total_referrals' => $user->referrals()->count(),
            'total_bonus'      => $user->transactions()->where('type', 'referral_bonus')->sum('amount'),
        ]);
    })->name('api.referral-stats');

    // Crypto prices (used by cryptoWidget)
    Route::get('/crypto-prices', function () {
        $prices = \App\Models\CryptoPrice::latest()->take(5)->get();
        return response()->json($prices);
    })->name('api.crypto-prices');

    // Trading price (used by BTC price ticker)
    Route::get('/trading/price', function () {
        $btc = \App\Models\CryptoPrice::where('symbol', 'BTC')->latest()->first();
        return response()->json([
            'price_kes' => $btc->price_kes ?? 0,
            'symbol'    => 'BTC'
        ]);
    })->name('api.trading.price');

    // Notifications unread count
    Route::get('/notifications/unread-count', function (Request $request) {
        return response()->json(['count' => $request->user()->unreadNotifications->count()]);
    })->name('api.notifications.unread');
});

// ==============================================
// API endpoints used by dashboard widgets
// ==============================================

Route::middleware('auth:sanctum')->group(function () {

    // Wallet balance (refresh button)
    Route::get('/wallet', function (Request $request) {
        return response()->json([
            'balance' => $request->user()->wallet->balance ?? 0,
        ]);
    })->name('api.wallet');

    // Referral stats (referral widget)
    Route::get('/referral-stats', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'total_referrals' => $user->referrals()->count(),
            'total_bonus'     => $user->transactions()->where('type', 'referral_bonus')->sum('amount'),
        ]);
    })->name('api.referral-stats');

    // Crypto prices (crypto widget)
    Route::get('/crypto-prices', function () {
        return response()->json(\App\Models\CryptoPrice::latest()->take(5)->get());
    })->name('api.crypto-prices');

    // BTC price (trading widget)
    Route::get('/trading/price', function () {
        $btc = \App\Models\CryptoPrice::where('symbol', 'BTC')->latest()->first();
        return response()->json(['price_kes' => $btc->price_kes ?? 0]);
    })->name('api.trading.price');

    // Unread notifications count (bell icon)
    Route::get('/notifications/unread-count', function (Request $request) {
        return response()->json(['count' => $request->user()->unreadNotifications->count()]);
    });
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/enterprise/machines', [App\Http\Controllers\Api\EnterpriseMachineController::class, 'index']);
    Route::get('/enterprise/machines/{code}', [App\Http\Controllers\Api\EnterpriseMachineController::class, 'show']);
});
