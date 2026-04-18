<?php

use App\Http\Controllers\Api\AuthController;
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
use App\Http\Controllers\Api\InvestmentController;
use App\Http\Controllers\LotteryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Health Check
Route::get('/health', function () {
    return response()->json(['status' => 'healthy', 'timestamp' => now()]);
});

// API V1
Route::prefix('v1')->group(function () {
    // Public routes (no auth)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/mpesa/callback', [MpesaController::class, 'callback'])->name('api.mpesa.callback');
    Route::get('/crypto/prices', [CryptoController::class, 'prices']);
    Route::get('/machine/list', [MachineController::class, 'publicList']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Protected routes (auth:sanctum)
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        // Auth
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/user/profile', [AuthController::class, 'updateProfile']);
        Route::put('/user/password', [AuthController::class, 'updatePassword']);

        // Wallet (including multi-currency)
        Route::prefix('wallet')->group(function () {
            Route::get('/balance', [WalletController::class, 'balance']);
            Route::get('/transactions', [WalletController::class, 'transactions']);
            Route::post('/transfer', [WalletController::class, 'transfer']);
            Route::get('/summary', [WalletController::class, 'summary']);
            Route::post('/currency', [WalletController::class, 'setCurrency']); // new
        });

        // Trading, Machines, KYC, Deposit, Withdrawal, Transactions, Referrals, Crypto, Notifications
        // ... (keep existing routes)
    });
});

// API V2 (rate limited as well)
Route::prefix('v2')->group(function () {
    Route::get('/health', function () { return response()->json(['version' => '2.0', 'status' => 'healthy']); });
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::get('/user/profile', function (Request $request) {
            $user = $request->user();
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'kyc_status' => $user->kyc_status,
                'is_verified' => $user->is_verified,
                'wallet_balance' => $user->wallet->balance ?? 0,
            ]);
        });
        Route::get('/machines', [App\Http\Controllers\Api\V2\MachineController::class, 'index']);
        Route::get('/machines/{code}', [App\Http\Controllers\Api\V2\MachineController::class, 'show']);
        Route::post('/machines/{machine}/invest', [App\Http\Controllers\Api\V2\MachineController::class, 'invest']);
        Route::get('/portfolio/summary', [App\Http\Controllers\Api\V2\PortfolioController::class, 'summary']);
        Route::get('/wealth-tax/history', [App\Http\Controllers\Api\V2\WealthTaxController::class, 'history']);
    });
});

// API Info
Route::get('/v1/info', function () {
    return response()->json([
        'version' => '1.0.0',
        'name' => 'Racksephnox Crypto API',
        'description' => 'Divine Golden Spirit API',
        'endpoints' => [
            'auth' => '/v1/login, /v1/register, /v1/logout',
            'wallet' => '/v1/wallet/*',
            'machines' => '/v1/machines/*',
            'trading' => '/v1/trading/*',
            'kyc' => '/v1/kyc/*',
            'deposit' => '/v1/deposit/*',
            'withdrawal' => '/v1/withdrawal/*',
            'notifications' => '/v1/notifications/*',
        ]
    ]);
});

// LOTTERY API (with rate limiting)
Route::middleware(['auth:sanctum', 'throttle:60,1'])->prefix('lottery')->group(function () {
    Route::get('/jackpot', [LotteryController::class, 'jackpotStatus']);
    Route::get('/free-spin-status', function () {
        $game = \App\Models\LotteryGame::where('is_active', true)->first();
        $service = new \App\Services\LotteryService($game);
        return response()->json(['available' => $service->canUseFreeSpin(Auth::user())]);
    });
    Route::get('/next-free-spin', function () {
        $game = \App\Models\LotteryGame::where('is_active', true)->first();
        $service = new \App\Services\LotteryService($game);
        return response()->json(['hours' => $service->getNextFreeSpinHours(Auth::user())]);
    });
    Route::get('/user-stats', function () {
        $user = Auth::user();
        $stats = [
            'total_spins' => \App\Models\LotterySpin::where('user_id', $user->id)->count(),
            'total_won' => \App\Models\LotterySpin::where('user_id', $user->id)->sum('win_amount'),
            'mini_jackpot_hits' => \App\Models\LotterySpin::where('user_id', $user->id)->where('mini_jackpot_hit', true)->count(),
            'super_jackpot_hits' => \App\Models\LotterySpin::where('user_id', $user->id)->where('super_jackpot_hit', true)->count(),
            'total_tax_contributed' => \App\Models\LotterySpin::where('user_id', $user->id)->sum('tax_contribution'),
        ];
        return response()->json($stats);
    });
    Route::post('/verify', [LotteryController::class, 'verifySpin']);
    Route::get('/prediction', [LotteryController::class, 'prediction']);
    Route::get('/bonus-wheel/segments', function () {
        $wheel = \App\Models\LotteryBonusWheel::where('is_active', true)->first();
        return response()->json($wheel ? $wheel->segments : []);
    });
    Route::post('/demo-spin', function (\Illuminate\Http\Request $request) {
        $names = ['divine_sword','divine_bell','golden_flower','frequency_8888','frequency_7777','taurus','tree_of_life','divine_star'];
        $symbols = [];
        for ($i=0;$i<3;$i++) $symbols[] = (object)['name' => $names[array_rand($names)], 'display_name' => '', 'icon' => ''];
        $winMultiplier = rand(0, 50);
        $miniJackpot = rand(1,100) <= 5;
        $superJackpot = rand(1,10000) === 1;
        $winAmount = $superJackpot ? 200000 : ($miniJackpot ? 5000 : $winMultiplier * $request->bet);
        return response()->json([
            'success' => true,
            'symbols' => array_map(fn($s) => ['name' => $s->name, 'display_name' => $s->name, 'icon' => ''], $symbols),
            'win_amount' => $winAmount,
            'mini_jackpot' => $miniJackpot,
            'super_jackpot' => $superJackpot,
            'free_spin_trigger' => false,
        ]);
    });
});
