<?php
use App\Http\Controllers\LotteryController;
use App\Http\Controllers\LotteryBonusWheelController;
use App\Http\Controllers\LotteryMissionController;
use App\Http\Controllers\LotterySocialController;
use App\Http\Controllers\LotteryTournamentController;
use Illuminate\Support\Facades\Route;
Route::middleware(['auth', 'verified'])->prefix('lottery')->name('lottery.')->group(function () {
    Route::get('/', [LotteryController::class, 'index'])->name('index');
    Route::post('/spin', [LotteryController::class, 'spin'])->name('spin');
    Route::post('/free-spin', [LotteryController::class, 'freeSpin'])->name('free-spin');
    Route::post('/buy-bonus', [LotteryController::class, 'buyBonus'])->name('buy-bonus');
    Route::get('/history', [LotteryController::class, 'history'])->name('history');
    Route::get('/leaderboard/{period?}', [LotteryController::class, 'leaderboard'])->name('leaderboard');
    Route::get('/achievements', [LotteryController::class, 'achievements'])->name('achievements');
    Route::get('/dashboard', [LotteryController::class, 'dashboard'])->name('dashboard');
    Route::get('/bonus-wheel', [LotteryBonusWheelController::class, 'index'])->name('bonus-wheel');
    Route::post('/bonus-wheel/spin', [LotteryBonusWheelController::class, 'spin'])->name('bonus-wheel.spin');
    Route::get('/missions', [LotteryMissionController::class, 'index'])->name('missions');
    Route::get('/social-feed', [LotterySocialController::class, 'feed'])->name('social-feed');
    Route::get('/tournaments', [LotteryTournamentController::class, 'index'])->name('tournaments');
});
