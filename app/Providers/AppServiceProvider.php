<?php

namespace App\Providers;

use App\Services\Trading\TradingEngine;
use App\Services\Trading\TradingBonusService;
use App\Models\TradingPair;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Trading engine – requires a default pair (BTCUSDT)
        $this->app->singleton(TradingEngine::class, function ($app) {
            $pair = TradingPair::firstOrCreate(
                ['symbol' => 'BTCUSDT'],
                [
                    'base_currency' => 'BTC',
                    'quote_currency' => 'USDT',
                    'min_trade_amount' => 0.0001,
                    'max_trade_amount' => 100,
                    'tick_size' => 0.0001,
                    'is_active' => true,
                ]
            );
            return new TradingEngine($pair);
        });

        $this->app->singleton(TradingBonusService::class);
    }

    public function boot(): void
    {
        //
    }
}
