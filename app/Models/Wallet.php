<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id', 'balance', 'currency', 'balance_usd', 'balance_eur',
        'balance_btc', 'balance_eth', 'balance_usdt'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'balance_usd' => 'decimal:8',
        'balance_eur' => 'decimal:8',
        'balance_btc' => 'decimal:8',
        'balance_eth' => 'decimal:8',
        'balance_usdt' => 'decimal:8',
    ];

    protected $attributes = [
        'currency' => 'KES',
        'balance' => 0,
        'balance_usd' => 0,
        'balance_eur' => 0,
        'balance_btc' => 0,
        'balance_eth' => 0,
        'balance_usdt' => 0,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getBalanceInCurrency($currency)
    {
        if ($currency === 'KES') return $this->balance;
        $field = 'balance_' . strtolower($currency);
        if (in_array($currency, ['USD', 'EUR', 'BTC', 'ETH', 'USDT']) && isset($this->$field)) {
            return $this->$field;
        }
        $rates = ['USD' => 0.0075, 'EUR' => 0.0069, 'BTC' => 0.00000011, 'ETH' => 0.0000018, 'USDT' => 0.0075];
        return $this->balance * ($rates[$currency] ?? 1);
    }

    public function setBalanceAttribute($value)
    {
        $this->attributes['balance'] = $value;
        $rates = ['USD' => 0.0075, 'EUR' => 0.0069, 'BTC' => 0.00000011, 'ETH' => 0.0000018, 'USDT' => 0.0075];
        $this->attributes['balance_usd'] = $value * $rates['USD'];
        $this->attributes['balance_eur'] = $value * $rates['EUR'];
        $this->attributes['balance_btc'] = $value * $rates['BTC'];
        $this->attributes['balance_eth'] = $value * $rates['ETH'];
        $this->attributes['balance_usdt'] = $value * $rates['USDT'];
    }
}
