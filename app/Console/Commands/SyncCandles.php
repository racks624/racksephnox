<?php
namespace App\Console\Commands;
use App\Models\TradingPair;
use App\Services\Trading\ChartService;
use Illuminate\Console\Command;
class SyncCandles extends Command
{
    protected $signature = 'trading:sync-candles {pair=BTCUSDT} {interval=1h} {limit=500}';
    protected $description = 'Sync historical candlestick data';
    public function handle()
    {
        $pair = TradingPair::where('symbol', $this->argument('pair'))->firstOrFail();
        $service = new ChartService();
        $service->syncHistoricalData($pair, $this->argument('interval'), $this->argument('limit'));
        $this->info("Candles synced for {$pair->symbol}");
    }
}
