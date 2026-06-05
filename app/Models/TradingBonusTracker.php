<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradingBonusTracker extends Model
{
    protected $fillable = ['user_id', 'trade_count_24h', 'last_bonus_awarded_at'];

    protected $casts = [
        'trade_count_24h' => 'integer',
        'last_bonus_awarded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
