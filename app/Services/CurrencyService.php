<?php

namespace App\Services;

class CurrencyService
{
    /**
     * Supported currencies with their symbols and exchange rates (relative to KES)
     */
    protected $currencies = [
        'KES' => ['symbol' => 'KES', 'name' => 'Kenyan Shilling', 'rate' => 1, 'decimals' => 2],
        'USD' => ['symbol' => '$', 'name' => 'US Dollar', 'rate' => 0.0077, 'decimals' => 2],
        'EUR' => ['symbol' => '€', 'name' => 'Euro', 'rate' => 0.0071, 'decimals' => 2],
        'GBP' => ['symbol' => '£', 'name' => 'British Pound', 'rate' => 0.0061, 'decimals' => 2],
        'UGX' => ['symbol' => 'USh', 'name' => 'Ugandan Shilling', 'rate' => 28.5, 'decimals' => 0],
        'TZS' => ['symbol' => 'TSh', 'name' => 'Tanzanian Shilling', 'rate' => 19.2, 'decimals' => 0],
        'RWF' => ['symbol' => 'FRw', 'name' => 'Rwandan Franc', 'rate' => 9.8, 'decimals' => 0],
        'ZAR' => ['symbol' => 'R', 'name' => 'South African Rand', 'rate' => 0.14, 'decimals' => 2],
        'NGN' => ['symbol' => '₦', 'name' => 'Nigerian Naira', 'rate' => 11.5, 'decimals' => 2],
        'GHS' => ['symbol' => '₵', 'name' => 'Ghanaian Cedi', 'rate' => 0.11, 'decimals' => 2],
    ];

    /**
     * Convert an amount from KES to target currency.
     */
    public function convert($amount, $toCurrency = 'KES'): float
    {
        $toRate = $this->currencies[$toCurrency]['rate'] ?? 1;
        return round($amount * $toRate, $this->currencies[$toCurrency]['decimals'] ?? 2);
    }

    /**
     * Convert from any currency to KES.
     */
    public function convertToKes($amount, $fromCurrency = 'KES'): float
    {
        $fromRate = $this->currencies[$fromCurrency]['rate'] ?? 1;
        if ($fromRate == 0) return 0;
        return round($amount / $fromRate, 2);
    }

    /**
     * Get formatted amount with currency symbol.
     */
    public function format($amount, $currency = 'KES'): string
    {
        $symbol = $this->currencies[$currency]['symbol'] ?? $currency;
        $decimals = $this->currencies[$currency]['decimals'] ?? 2;
        $formatted = number_format($amount, $decimals);
        return "{$symbol} {$formatted}";
    }

    /**
     * Get the symbol for a currency.
     */
    public function getSymbol($currency): string
    {
        return $this->currencies[$currency]['symbol'] ?? $currency;
    }

    /**
     * Get all available currencies.
     */
    public function getAvailableCurrencies(): array
    {
        return array_keys($this->currencies);
    }

    /**
     * Get exchange rate for a currency (relative to KES).
     */
    public function getRate($currency): float
    {
        return $this->currencies[$currency]['rate'] ?? 1;
    }
}
