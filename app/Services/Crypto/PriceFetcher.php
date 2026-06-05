<?php

namespace App\Services\Crypto;

use App\Models\CryptoPrice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PriceFetcher
{
    protected $api;
    protected $config;

    public function __construct()
    {
        $this->config = config('crypto');
        $this->api = $this->config['default'];
    }

    /**
     * Fetch current prices for all supported coins
     */
    public function fetchAll()
    {
        $supported = $this->config['supported'];
        $ids = implode(',', array_values($supported));

        $response = Http::withOptions([
            'base_uri' => $this->config['coingecko']['base_url'],
        ])->get('/simple/price', [
            'ids' => $ids,
            'vs_currencies' => 'usd,kes',
            'x_cg_pro_api_key' => $this->config['coingecko']['api_key'],
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch crypto prices');
        }

        $prices = $response->json();
        $kesRate = $this->getUsdToKesRate(); // You can implement this or use a fixed rate

        foreach ($this->config['supported'] as $symbol => $id) {
            if (isset($prices[$id])) {
                $priceUsd = $prices[$id]['usd'];
                $priceKes = $prices[$id]['kes'] ?? ($priceUsd * $kesRate);

                CryptoPrice::updateOrCreate(
                    ['symbol' => $symbol],
                    [
                        'name' => $id,
                        'price_usd' => $priceUsd,
                        'price_kes' => $priceKes,
                        'last_updated' => now(),
                    ]
                );
            }
        }

        Cache::put('crypto_prices', CryptoPrice::all(), now()->addMinutes($this->config['cache_duration']));
    }

    protected function getUsdToKesRate()
    {
        // You could fetch from a forex API or use a fixed rate
        return 130; // Example: 1 USD = 130 KES
    }
}
