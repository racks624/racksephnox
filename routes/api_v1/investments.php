<?php

use App\Http\Controllers\Api\InvestmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/plans', [InvestmentController::class, 'plans']);
    Route::post('/invest', [InvestmentController::class, 'store']);
    Route::get('/my', [InvestmentController::class, 'myInvestments']);
});
