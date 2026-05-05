<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotteryDailyStreak extends Model
{
    protected $table = 'lottery_daily_streaks';

    protected $fillable = [
        'user_id', 'streak_count', 'last_login_date', 'reward_claimed'
    ];

    protected $casts = [
        'last_login_date' => 'date',
        'reward_claimed' => 'boolean',
        'streak_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
