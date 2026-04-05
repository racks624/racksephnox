<?php

namespace App\Console\Commands;

use App\Models\CryptoPrice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SyncCryptoPrices extends Command
{
    protected $signature = 'crypto:sync-prices';
    protected $description = 'Fetch latest cryptocurrency prices from CoinGecko';

    // List of supported coins (symbol => CoinGecko ID)
    protected $coins = [
        'BTC' => 'bitcoin',
        'ETH' => 'ethereum',
        'USDT' => 'tether',
        'BNB' => 'binancecoin',
        'SOL' => 'solana',
        'XRP' => 'ripple',
    ];

    // Exchange rate from USD to KES (you can fetch from a forex API)
    protected $usdToKes = 130; // placeholder; in production, fetch from a free API

    public function handle()
    {
        $ids = implode(',', array_values($this->coins));
        $url = "https://api.coingecko.com/api/v3/simple/price?ids={$ids}&vs_currencies=usd&include_24hr_change=true";

        try {
            $response = Http::timeout(10)->get($url);
            if (!$response->successful()) {
                $this->error('CoinGecko API error: ' . $response->status());
                return 1;
            }

            $data = $response->json();

            foreach ($this->coins as $symbol => $id) {
                if (isset($data[$id])) {
                    $priceUsd = $data[$id]['usd'];
                    $change24h = $data[$id]['usd_24h_change'] ?? 0;
                    $priceKes = $priceUsd * $this->usdToKes;

                    CryptoPrice::updateOrCreate(
                        ['symbol' => $symbol],
                        [
                            'name' => ucfirst($id),
                            'price_usd' => $priceUsd,
                            'price_kes' => $priceKes,
                            'percent_change_24h' => $change24h,
                            'last_updated' => now(),
                        ]
                    );
                }
            }

            $this->info('Crypto prices synced successfully.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to sync crypto prices: ' . $e->getMessage());
            return 1;
        }
    }
}
