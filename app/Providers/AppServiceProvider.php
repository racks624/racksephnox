<?php

namespace App\Providers;

use App\Services\Trading\TradingEngine;
use App\Services\Trading\TradingBonusService;
use App\Models\TradingPair;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Skip trading engine registration during migrations and key generation
        $isArtisanCommand = $this->app->runningInConsole();
        $commandName = $isArtisanCommand ? ($_SERVER['argv'][1] ?? '') : '';
        
        $isSafeCommand = in_array($commandName, ['migrate', 'key:generate', 'migrate:fresh', 'migrate:refresh', 'db:seed']);
        
        if ($isArtisanCommand && $isSafeCommand) {
            // Register dummy bindings that don't query database
            $this->app->singleton(TradingEngine::class, function ($app) {
                $tempPair = new TradingPair();
                $tempPair->exists = false; // Mark as not persisted
                return new TradingEngine($tempPair);
            });
            $this->app->singleton(TradingBonusService::class, function ($app) {
                return new TradingBonusService();
            });
            return;
        }
        
        // Normal registration for web requests
        // Trading engine – requires a default pair (BTCUSDT)
        $this->app->singleton(TradingEngine::class, function ($app) {
            // Check if the trading_pairs table exists to avoid query errors during migrations
            if (!Schema::hasTable('trading_pairs')) {
                // Table doesn't exist yet, return a dummy engine with a temporary pair
                $tempPair = new TradingPair();
                $tempPair->symbol = 'BTCUSDT';
                $tempPair->base_currency = 'BTC';
                $tempPair->quote_currency = 'USDT';
                $tempPair->min_trade_amount = 0.0001;
                $tempPair->max_trade_amount = 100;
                $tempPair->tick_size = 0.0001;
                $tempPair->is_active = true;
                return new TradingEngine($tempPair);
            }

            // Table exists, proceed normally
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
