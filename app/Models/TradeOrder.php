<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'side', 'order_type', 'amount_btc', 'filled_amount', 'amount_kes', 'filled_kes',
        'price_per_btc', 'limit_price', 'stop_price', 'status'
    ];

    protected $casts = [
        'amount_btc' => 'decimal:8',
        'filled_amount' => 'decimal:8',
        'amount_kes' => 'decimal:2',
        'filled_kes' => 'decimal:2',
        'price_per_btc' => 'decimal:2',
        'limit_price' => 'decimal:2',
        'stop_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper to get remaining amount
    public function getRemainingAmount()
    {
        return $this->amount_btc - $this->filled_amount;
    }
}
