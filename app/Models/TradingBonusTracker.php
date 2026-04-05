<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingBonusTracker extends Model
{
    use HasFactory;

    protected $table = 'trading_bonus_tracker';

    protected $fillable = [
        'user_id', 'trade_count_24h', 'last_bonus_awarded_at'
    ];

    protected $casts = [
        'last_bonus_awarded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
