<?php

use App\Http\Controllers\Api\TradingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/balance', [TradingController::class, 'balance']);
    Route::get('/price', [TradingController::class, 'price']);
    Route::post('/buy', [TradingController::class, 'buy']);
    Route::post('/sell', [TradingController::class, 'sell']);
    Route::get('/orders', [TradingController::class, 'orders']);
});
