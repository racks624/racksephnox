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

// Public routes
Route::get('/', function () { return view('welcome'); })->name('home');
Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');
Route::get('/guide', [GuideController::class, 'index'])->name('guide');
Route::post('/mpesa/callback', [MpesaController::class, 'callback'])->name('mpesa.callback');

// Legal Pages
Route::controller(App\Http\Controllers\LegalPagesController::class)->group(function () {
    Route::get('/terms', 'terms')->name('terms');
    Route::get('/privacy', 'privacy')->name('privacy');
    Route::get('/cookies', 'cookies')->name('cookies');
    Route::get('/compliance', 'compliance')->name('compliance');
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/investments', [InvestmentWebController::class, 'index'])->name('web.investments');
    Route::post('/investments', [InvestmentWebController::class, 'store'])->name('web.investments.store');
    Route::post('/investments/{investment}/early-withdraw', [InvestmentWebController::class, 'earlyWithdraw'])->name('web.investments.early-withdraw');
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/export', [TransactionController::class, 'exportCsv'])->name('transactions.export');
    Route::get('/kyc', [KycController::class, 'index'])->name('kyc');
    Route::post('/kyc/upload', [KycController::class, 'upload'])->name('kyc.upload');
    Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals');
    Route::get('/refer/{code}', [ReferralController::class, 'show'])->name('referral.show');
    Route::get('/deposit', [DepositController::class, 'showForm'])->name('deposit.form');
    Route::post('/deposit/submit', [DepositController::class, 'submitRequest'])->name('deposit.submit');
    Route::get('/withdrawal', [WithdrawalController::class, 'showForm'])->name('withdrawal.form');
    Route::post('/withdrawal/submit', [WithdrawalController::class, 'submitRequest'])->name('withdrawal.submit');
    Route::resource('bank-accounts', BankAccountController::class);
    
    // Profile routes (ONLY ONCE)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
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
        Route::get('/investment/{investment}/status', [MachineController::class, 'status'])->name('status');
        Route::get('/my-investments', [MachineController::class, 'myInvestments'])->name('my-investments');
    });
    
    // Trading
    Route::prefix('trading')->name('trading.')->group(function () {
        Route::get('/', [TradingController::class, 'index'])->name('index');
        Route::post('/transfer', [TradingController::class, 'transfer'])->name('transfer');
        Route::post('/cancel/{order}', [TradingController::class, 'cancelOrder'])->name('cancel');
    });
    
    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('markAllRead');
    });
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('plans', InvestmentPlanController::class);
    Route::get('kyc', [AdminKycController::class, 'index'])->name('kyc.index');
    Route::get('kyc/{document}', [AdminKycController::class, 'show'])->name('kyc.show');
    Route::post('kyc/{document}/approve', [AdminKycController::class, 'approve'])->name('kyc.approve');
    Route::post('kyc/{document}/reject', [AdminKycController::class, 'reject'])->name('kyc.reject');
    Route::prefix('deposits')->name('deposits.')->group(function () {
        Route::get('/', [DepositVerificationController::class, 'index'])->name('index');
        Route::post('/{deposit}/verify', [DepositVerificationController::class, 'verify'])->name('verify');
        Route::post('/{deposit}/reject', [DepositVerificationController::class, 'reject'])->name('reject');
    });
    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [AdminWithdrawalController::class, 'index'])->name('index');
        Route::post('/{withdrawal}/process', [AdminWithdrawalController::class, 'process'])->name('process');
        Route::post('/{withdrawal}/complete', [AdminWithdrawalController::class, 'complete'])->name('complete');
        Route::post('/{withdrawal}/reject', [AdminWithdrawalController::class, 'reject'])->name('reject');
    });
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export/users', [ReportController::class, 'exportUsers'])->name('export.users');
        Route::get('/export/transactions', [ReportController::class, 'exportTransactions'])->name('export.transactions');
        Route::get('/export/investments', [ReportController::class, 'exportInvestments'])->name('export.investments');
        Route::get('/export/trading', [ReportController::class, 'exportTrading'])->name('export.trading');
        Route::get('/export/pdf', [ReportController::class, 'exportPdfReport'])->name('export.pdf');
    });
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/update', [SettingsController::class, 'update'])->name('update');
        Route::post('/maintenance', [SettingsController::class, 'toggleMaintenance'])->name('maintenance');
    });
});

// Include Breeze authentication routes
require __DIR__.'/auth.php';

// Unified investment view (shows both legacy and machine investments)
Route::get('/my-investments', [App\Http\Controllers\InvestmentWebController::class, 'index'])->name('investments.unified')->middleware('auth');
