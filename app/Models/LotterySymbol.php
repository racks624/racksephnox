<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotterySymbol extends Model
{
    protected $fillable = ['name', 'display_name', 'icon', 'weight', 'is_wild', 'is_scatter'];
    protected $casts = ['is_wild' => 'boolean', 'is_scatter' => 'boolean'];

    public function games()
    {
        return $this->belongsToMany(LotteryGame::class, 'lottery_payouts')
                    ->withPivot('count', 'payout_multiplier');
    }
}
