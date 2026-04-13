<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InvestmentPlanController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\DepositVerificationController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| All routes here are prefixed with /admin and protected by 'auth' and 'admin' middleware.
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [DashboardController::class, 'stats'])->name('stats');

    // Users
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::post('users/export', [UserController::class, 'export'])->name('users.export');

    // Investment Plans
    Route::resource('plans', InvestmentPlanController::class);
    Route::post('plans/export', [InvestmentPlanController::class, 'export'])->name('plans.export');

    // KYC Verification
    Route::get('kyc', [KycController::class, 'index'])->name('kyc.index');
    Route::get('kyc/{document}', [KycController::class, 'show'])->name('kyc.show');
    Route::post('kyc/{document}/approve', [KycController::class, 'approve'])->name('kyc.approve');
    Route::post('kyc/{document}/reject', [KycController::class, 'reject'])->name('kyc.reject');

    // Deposit Verification
    Route::get('deposits', [DepositVerificationController::class, 'index'])->name('deposits.index');
    Route::post('deposits/{deposit}/verify', [DepositVerificationController::class, 'verify'])->name('deposits.verify');
    Route::post('deposits/{deposit}/reject', [DepositVerificationController::class, 'reject'])->name('deposits.reject');

    // Withdrawal Processing
    Route::get('withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('withdrawals/{withdrawal}/process', [WithdrawalController::class, 'process'])->name('withdrawals.process');
    Route::post('withdrawals/{withdrawal}/complete', [WithdrawalController::class, 'complete'])->name('withdrawals.complete');
    Route::post('withdrawals/{withdrawal}/reject', [WithdrawalController::class, 'reject'])->name('withdrawals.reject');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('reports/export', [ReportController::class, 'export'])->name('reports.export');

    // System Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/cache-clear', [SettingsController::class, 'clearCache'])->name('settings.cache-clear');
    Route::post('settings/maintenance', [SettingsController::class, 'toggleMaintenance'])->name('settings.maintenance');
});

    // Reports exports
    Route::get('reports/export/users', [ReportController::class, 'exportUsers'])->name('reports.export.users');
    Route::get('reports/export/transactions', [ReportController::class, 'exportTransactions'])->name('reports.export.transactions');
    Route::get('reports/export/investments', [ReportController::class, 'exportInvestments'])->name('reports.export.investments');
    Route::get('reports/export/trading', [ReportController::class, 'exportTrading'])->name('reports.export.trading');
    Route::get('reports/export/pdf', [ReportController::class, 'exportPdfReport'])->name('reports.export.pdf');

// Lottery Management (Admin)
Route::prefix('lottery')->name('lottery.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\LotteryController::class, 'index'])->name('index');
    Route::get('/game/{game}/edit', [App\Http\Controllers\Admin\LotteryController::class, 'editGame'])->name('edit-game');
    Route::put('/game/{game}', [App\Http\Controllers\Admin\LotteryController::class, 'updateGame'])->name('update-game');
    Route::get('/symbol/{symbol}/edit', [App\Http\Controllers\Admin\LotteryController::class, 'editSymbol'])->name('edit-symbol');
    Route::put('/symbol/{symbol}', [App\Http\Controllers\Admin\LotteryController::class, 'updateSymbol'])->name('update-symbol');
    Route::get('/stats', [App\Http\Controllers\Admin\LotteryController::class, 'stats'])->name('stats');
});

// Lottery Management (Admin)
Route::prefix('lottery')->name('lottery.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\LotteryController::class, 'index'])->name('index');
    Route::get('/game/{game}/edit', [App\Http\Controllers\Admin\LotteryController::class, 'editGame'])->name('edit-game');
    Route::put('/game/{game}', [App\Http\Controllers\Admin\LotteryController::class, 'updateGame'])->name('update-game');
    Route::get('/symbol/{symbol}/edit', [App\Http\Controllers\Admin\LotteryController::class, 'editSymbol'])->name('edit-symbol');
    Route::put('/symbol/{symbol}', [App\Http\Controllers\Admin\LotteryController::class, 'updateSymbol'])->name('update-symbol');
    Route::get('/stats', [App\Http\Controllers\Admin\LotteryController::class, 'stats'])->name('stats');
});

// Lottery Management (Admin)
Route::prefix('lottery')->name('lottery.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\LotteryController::class, 'index'])->name('index');
    Route::get('/game/{game}/edit', [App\Http\Controllers\Admin\LotteryController::class, 'editGame'])->name('edit-game');
    Route::put('/game/{game}', [App\Http\Controllers\Admin\LotteryController::class, 'updateGame'])->name('update-game');
    Route::get('/symbol/{symbol}/edit', [App\Http\Controllers\Admin\LotteryController::class, 'editSymbol'])->name('edit-symbol');
    Route::put('/symbol/{symbol}', [App\Http\Controllers\Admin\LotteryController::class, 'updateSymbol'])->name('update-symbol');
    Route::get('/stats', [App\Http\Controllers\Admin\LotteryController::class, 'stats'])->name('stats');
});
