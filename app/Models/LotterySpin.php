<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotterySpin extends Model
{
    protected $fillable = [
        'user_id', 'lottery_game_id', 'bet_amount', 'win_amount', 'result', 'status',
        'last_free_spin_at', 'free_spin_used', 'free_spins_remaining',
        'bonus_round_triggered', 'multiplier_active', 'scatter_count',
        'mini_jackpot_hit', 'super_jackpot_hit', 'free_spin_triggered', 'tax_contribution'
    ];
    protected $casts = [
        'result' => 'array',
        'bet_amount' => 'decimal:2',
        'win_amount' => 'decimal:2',
        'last_free_spin_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(LotteryGame::class, 'lottery_game_id');
    }
}
