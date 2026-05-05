<?php
namespace App\Services;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
class CurrencyService
{
    protected $rates = [];
    protected $default = 'KES';
    public function __construct()
    {
        $this->rates = Cache::remember('exchange_rates', 3600, function () {
            $response = Http::get('https://api.exchangerate.host/latest?base=KES');
            return $response->successful() ? $response->json('rates') : ['USD' => 0.0075, 'EUR' => 0.0069, 'GBP' => 0.0059];
        });
    }
    public function convert($amount, $toCurrency)
    {
        if ($toCurrency === $this->default) return $amount;
        $rate = $this->rates[$toCurrency] ?? 1;
        return round($amount * $rate, 2);
    }
    public function getSymbol($currency)
    {
        return match($currency) {
            'USD' => '$', 'EUR' => '€', 'GBP' => '£', default => 'KES'
        };
    }
}
