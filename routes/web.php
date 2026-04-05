<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvestmentWebController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\TradingController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InvestmentPlanController;
use App\Http\Controllers\Admin\KycController as AdminKycController;
use App\Http\Controllers\Admin\DepositVerificationController;
use App\Http\Controllers\Admin\WithdrawalController as AdminWithdrawalController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');
Route::get('/guide', [GuideController::class, 'index'])->name('guide');

// M-Pesa callback (public)
Route::post('/mpesa/callback', [MpesaController::class, 'callback'])->name('mpesa.callback');

// Authenticated routes (require login)
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Investments
    Route::get('/investments', [InvestmentWebController::class, 'index'])->name('web.investments');
    Route::post('/investments', [InvestmentWebController::class, 'store'])->name('web.investments.store');
    Route::post('/investments/{investment}/early-withdraw', [InvestmentWebController::class, 'earlyWithdraw'])->name('web.investments.early-withdraw');

    // Wallet
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/export', [TransactionController::class, 'exportCsv'])->name('transactions.export');

    // KYC
    Route::get('/kyc', [KycController::class, 'index'])->name('kyc');
    Route::post('/kyc/upload', [KycController::class, 'upload'])->name('kyc.upload');

    // M-Pesa
    Route::prefix('mpesa')->name('mpesa.')->group(function () {
        Route::get('/deposit', [MpesaController::class, 'showDepositForm'])->name('deposit');
        Route::post('/deposit', [MpesaController::class, 'initiateDeposit'])->name('deposit.initiate');
        Route::get('/withdraw', [MpesaController::class, 'showWithdrawalForm'])->name('withdraw');
        Route::post('/withdraw', [MpesaController::class, 'initiateWithdrawal'])->name('withdraw.initiate');
    });

    // Machines
    Route::prefix('machines')->name('machines.')->group(function () {
        Route::get('/', [MachineController::class, 'index'])->name('index');
        Route::get('/{code}', [MachineController::class, 'show'])->name('show');
        Route::post('/{machine}/invest', [MachineController::class, 'invest'])->name('invest');
    });

    // Trading
    Route::prefix('trading')->name('trading.')->group(function () {
        Route::get('/', [TradingController::class, 'index'])->name('index');
        Route::post('/transfer', [TradingController::class, 'transfer'])->name('transfer');
        Route::post('/cancel/{order}', [TradingController::class, 'cancelOrder'])->name('cancel');
    });

    // Referrals
    Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals');
    Route::get('/refer/{code}', [ReferralController::class, 'show'])->name('referral.show');

    // Deposit (Manual)
    Route::prefix('deposit')->name('deposit.')->group(function () {
        Route::get('/', [DepositController::class, 'showForm'])->name('form');
        Route::post('/submit', [DepositController::class, 'submitRequest'])->name('submit');
        Route::get('/status', [DepositController::class, 'status'])->name('status');
    });

    // Withdrawal
    Route::prefix('withdrawal')->name('withdrawal.')->group(function () {
        Route::get('/', [WithdrawalController::class, 'showForm'])->name('form');
        Route::post('/submit', [WithdrawalController::class, 'submitRequest'])->name('submit');
        Route::get('/history', [WithdrawalController::class, 'history'])->name('history');
    });

    // Bank Accounts
    Route::resource('bank-accounts', BankAccountController::class);

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('markAllRead');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/', [NotificationController::class, 'destroyAll'])->name('destroyAll');
        Route::get('/preferences', [NotificationController::class, 'preferences'])->name('preferences');
        Route::post('/preferences', [NotificationController::class, 'updatePreferences'])->name('preferences.update');
    });
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profile/notifications', [ProfileController::class, 'updateNotificationPreferences'])->name('profile.notifications.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Users
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    
    // Investment Plans
    Route::resource('plans', App\Http\Controllers\Admin\InvestmentPlanController::class);
    
    // KYC
    Route::prefix('kyc')->name('kyc.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\KycController::class, 'index'])->name('index');
        Route::get('/{document}', [App\Http\Controllers\Admin\KycController::class, 'show'])->name('show');
        Route::post('/{document}/approve', [App\Http\Controllers\Admin\KycController::class, 'approve'])->name('approve');
        Route::post('/{document}/reject', [App\Http\Controllers\Admin\KycController::class, 'reject'])->name('reject');
    });
    
    // Deposits
    Route::prefix('deposits')->name('deposits.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\DepositVerificationController::class, 'index'])->name('index');
        Route::post('/{deposit}/verify', [App\Http\Controllers\Admin\DepositVerificationController::class, 'verify'])->name('verify');
        Route::post('/{deposit}/reject', [App\Http\Controllers\Admin\DepositVerificationController::class, 'reject'])->name('reject');
    });
    
    // Withdrawals
    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('index');
        Route::post('/{withdrawal}/process', [App\Http\Controllers\Admin\WithdrawalController::class, 'process'])->name('process');
        Route::post('/{withdrawal}/complete', [App\Http\Controllers\Admin\WithdrawalController::class, 'complete'])->name('complete');
        Route::post('/{withdrawal}/reject', [App\Http\Controllers\Admin\WithdrawalController::class, 'reject'])->name('reject');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/export/users', [App\Http\Controllers\Admin\ReportController::class, 'exportUsers'])->name('export.users');
        Route::get('/export/transactions', [App\Http\Controllers\Admin\ReportController::class, 'exportTransactions'])->name('export.transactions');
        Route::get('/export/investments', [App\Http\Controllers\Admin\ReportController::class, 'exportInvestments'])->name('export.investments');
        Route::get('/export/trading', [App\Http\Controllers\Admin\ReportController::class, 'exportTrading'])->name('export.trading');
        Route::get('/export/pdf', [App\Http\Controllers\Admin\ReportController::class, 'exportPdfReport'])->name('export.pdf');
    });
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('index');
        Route::post('/update', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('update');
        Route::post('/maintenance', [App\Http\Controllers\Admin\SettingsController::class, 'toggleMaintenance'])->name('maintenance');
    });
});

// Social Trading
Route::middleware(['auth'])->prefix('social-trading')->name('social-trading.')->group(function () {
    Route::get('/leaderboard', [App\Http\Controllers\SocialTradingController::class, 'leaderboard'])->name('leaderboard');
    Route::get('/profile/{username}', [App\Http\Controllers\SocialTradingController::class, 'traderProfile'])->name('profile');
    Route::post('/follow/{user}', [App\Http\Controllers\SocialTradingController::class, 'follow'])->name('follow');
    Route::post('/unfollow/{user}', [App\Http\Controllers\SocialTradingController::class, 'unfollow'])->name('unfollow');
    Route::get('/followed', [App\Http\Controllers\SocialTradingController::class, 'followed'])->name('followed');
    Route::post('/update-settings/{user}', [App\Http\Controllers\SocialTradingController::class, 'updateSettings'])->name('update-settings');
    Route::get('/copy-history', [App\Http\Controllers\SocialTradingController::class, 'copyHistory'])->name('copy-history');
    Route::post('/update-profile', [App\Http\Controllers\SocialTradingController::class, 'updateProfile'])->name('update-profile');
});
