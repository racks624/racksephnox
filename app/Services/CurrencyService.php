<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.exchangerate-api.com/v4/latest/';

    public function __construct()
    {
        $this->apiKey = env('EXCHANGE_RATE_API_KEY', '');
    }

    public function getRate(string $from, string $to): float
    {
        if ($from === $to) return 1.0;
        $cacheKey = "exchange_rate_{$from}_{$to}";
        return Cache::remember($cacheKey, 3600, function () use ($from, $to) {
            try {
                $response = Http::get($this->baseUrl . $from);
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['rates'][$to] ?? 1.0;
                }
            } catch (\Exception $e) {
                // fallback to hardcoded rates (KES to USD approx)
                if ($from === 'KES' && $to === 'USD') return 0.0075;
                if ($from === 'USD' && $to === 'KES') return 133.33;
            }
            return 1.0;
        });
    }

    public function convert(float $amount, string $from, string $to): float
    {
        $rate = $this->getRate($from, $to);
        return round($amount * $rate, 2);
    }

    public function getUserBalanceInCurrency($user, string $currency): float
    {
        $wallet = $user->wallet;
        if ($currency === 'KES') return $wallet->balance;
        $field = "balance_" . strtolower($currency);
        if (in_array($currency, ['USD', 'EUR', 'BTC', 'ETH', 'USDT']) && isset($wallet->$field)) {
            return $wallet->$field;
        }
        // convert from KES
        return $this->convert($wallet->balance, 'KES', $currency);
    }
}
