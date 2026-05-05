<?php
namespace App\Services\Trading;
use App\Models\TradingCandle;
use App\Models\TradingPair;
use Illuminate\Support\Facades\Http;

class ChartService
{
    public function syncHistoricalData(TradingPair $pair, $interval = '1h', $limit = 500)
    {
        $url = "https://api.binance.com/api/v3/klines?symbol={$pair->symbol}&interval={$interval}&limit={$limit}";
        $response = Http::get($url);
        if (!$response->successful()) return;
        foreach ($response->json() as $k) {
            TradingCandle::updateOrCreate([
                'pair_id' => $pair->id, 'interval' => $interval, 'open_time' => date('Y-m-d H:i:s', $k[0]/1000)
            ], [
                'close_time' => date('Y-m-d H:i:s', $k[6]/1000),
                'open' => $k[1], 'high' => $k[2], 'low' => $k[3],
                'close' => $k[4], 'volume' => $k[5]
            ]);
        }
    }

    public function getCandles($pairId, $interval = '1h', $limit = 100)
    {
        return TradingCandle::where('pair_id', $pairId)->where('interval', $interval)
            ->orderBy('open_time')->limit($limit)->get();
    }
}
