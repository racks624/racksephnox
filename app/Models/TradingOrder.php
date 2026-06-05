<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradingOrder extends Model
{
    protected $fillable = [
        'user_id', 'type', 'amount', 'price', 'btc_amount', 'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'price' => 'decimal:2',
        'btc_amount' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
