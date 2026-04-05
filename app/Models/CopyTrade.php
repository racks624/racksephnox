<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CopyTrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_order_id', 'follower_id', 'trader_id', 'original_amount',
        'copied_amount', 'original_price', 'copied_kes', 'side', 'status'
    ];

    protected $casts = [
        'original_amount' => 'decimal:8',
        'copied_amount' => 'decimal:8',
        'original_price' => 'decimal:2',
        'copied_kes' => 'decimal:2',
    ];

    public function originalOrder()
    {
        return $this->belongsTo(TradeOrder::class, 'original_order_id');
    }

    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function trader()
    {
        return $this->belongsTo(User::class, 'trader_id');
    }
}
