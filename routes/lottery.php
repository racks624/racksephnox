<?php

use App\Http\Controllers\LotteryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('lottery')->name('lottery.')->group(function () {
    Route::get('/', [LotteryController::class, 'index'])->name('index');
    Route::post('/spin', [LotteryController::class, 'spin'])->name('spin');
    Route::post('/free-spin', [LotteryController::class, 'freeSpin'])->name('free-spin');
    Route::post('/buy-bonus', [LotteryController::class, 'buyBonus'])->name('buy-bonus');
    Route::post('/gamble', [LotteryController::class, 'gamble'])->name('gamble');
    Route::get('/history', [LotteryController::class, 'history'])->name('history');
    Route::get('/leaderboard/{period?}', [LotteryController::class, 'leaderboard'])->name('leaderboard');
    Route::get('/achievements', [LotteryController::class, 'achievements'])->name('achievements');
    Route::get('/dashboard', [LotteryController::class, 'dashboard'])->name('dashboard');
    Route::post('/verify', [LotteryController::class, 'verifySpin'])->name('verify');
});
