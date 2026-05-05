<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InvestmentPlanController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\DepositVerificationController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\LotteryController as AdminLotteryController;
use App\Http\Controllers\Admin\LotteryAnalyticsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [DashboardController::class, 'stats'])->name('stats');

    // Users
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::post('users/export', [UserController::class, 'export'])->name('users.export');

    // Investment Plans (machines)
    Route::resource('plans', InvestmentPlanController::class);
    Route::post('plans/export', [InvestmentPlanController::class, 'export'])->name('plans.export');

    // KYC
    Route::get('kyc', [KycController::class, 'index'])->name('kyc.index');
    Route::get('kyc/{document}', [KycController::class, 'show'])->name('kyc.show');
    Route::post('kyc/{document}/approve', [KycController::class, 'approve'])->name('kyc.approve');
    Route::post('kyc/{document}/reject', [KycController::class, 'reject'])->name('kyc.reject');

    // Deposits
    Route::get('deposits', [DepositVerificationController::class, 'index'])->name('deposits.index');
    Route::post('deposits/{deposit}/verify', [DepositVerificationController::class, 'verify'])->name('deposits.verify');
    Route::post('deposits/{deposit}/reject', [DepositVerificationController::class, 'reject'])->name('deposits.reject');

    // Withdrawals
    Route::get('withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('withdrawals/{withdrawal}/process', [WithdrawalController::class, 'process'])->name('withdrawals.process');
    Route::post('withdrawals/{withdrawal}/complete', [WithdrawalController::class, 'complete'])->name('withdrawals.complete');
    Route::post('withdrawals/{withdrawal}/reject', [WithdrawalController::class, 'reject'])->name('withdrawals.reject');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/cache-clear', [SettingsController::class, 'clearCache'])->name('settings.cache-clear');
    Route::post('settings/maintenance', [SettingsController::class, 'toggleMaintenance'])->name('settings.maintenance');

    // Lottery Admin
    Route::prefix('lottery')->name('lottery.')->group(function () {
        Route::get('/', [AdminLotteryController::class, 'index'])->name('index');
        Route::get('/symbols', [AdminLotteryController::class, 'symbols'])->name('symbols');
        Route::post('/symbols', [AdminLotteryController::class, 'updateSymbols'])->name('symbols.update');
        Route::get('/analytics', [LotteryAnalyticsController::class, 'index'])->name('analytics');
        Route::get('/export', [AdminLotteryController::class, 'exportConfig'])->name('export');
        Route::post('/import', [AdminLotteryController::class, 'importConfig'])->name('import');
        Route::get('/rtp-simulate', [AdminLotteryController::class, 'rtpSimulate'])->name('rtp-simulate');
    });
});
