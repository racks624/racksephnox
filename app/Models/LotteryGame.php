<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotteryGame extends Model
{
    protected $fillable = [
        'name', 'description', 'ticket_price', 'settings', 'is_active',
        'min_bet', 'max_bet', 'reel_config', 'paylines', 'bonus_symbol_id',
        'free_spins_award', 'jackpot_contribution_rate', 'progressive_jackpot'
    ];
    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'reel_config' => 'array',
        'paylines' => 'array',
        'progressive_jackpot' => 'decimal:2',
    ];

    public function symbols()
    {
        return $this->belongsToMany(LotterySymbol::class, 'lottery_payouts')
                    ->withPivot('count', 'payout_multiplier');
    }

    public function bonusSymbol()
    {
        return $this->belongsTo(LotterySymbol::class, 'bonus_symbol_id');
    }

    public function spins()
    {
        return $this->hasMany(LotterySpin::class);
    }
}
