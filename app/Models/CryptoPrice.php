<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CryptoPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'symbol', 'name', 'price_usd', 'price_kes', 'percent_change_24h', 'last_updated'
    ];

    protected $casts = [
        'price_usd' => 'decimal:8',
        'price_kes' => 'decimal:2',
        'percent_change_24h' => 'decimal:2',
        'last_updated' => 'datetime',
    ];
}
