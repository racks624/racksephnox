<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LotteryGame extends Model
{
    protected $table = 'lottery_games';

    protected $fillable = [
        'name', 'description', 'ticket_price', 'settings', 'is_active',
        'min_bet', 'max_bet', 'reel_config', 'paylines', 'bonus_symbol_id',
        'free_spins_award', 'jackpot_contribution_rate', 'progressive_jackpot',
        'base_rtp', 'vip_rtp', 'promo_rtp',
        'volatility', 'max_daily_loss', 'max_weekly_loss', 'max_monthly_loss',
        'max_win_cap', 'cool_down_minutes', 'session_timeout_minutes',
        'enable_free_spins', 'enable_bonus_buy', 'bonus_buy_price'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'reel_config' => 'array',
        'paylines' => 'array',
        'progressive_jackpot' => 'decimal:2',
        'base_rtp' => 'decimal:2',
        'vip_rtp' => 'decimal:2',
        'promo_rtp' => 'decimal:2',
        'min_bet' => 'decimal:2',
        'max_bet' => 'decimal:2',
        'ticket_price' => 'decimal:2',
        'volatility' => 'string',
        'max_daily_loss' => 'decimal:2',
        'max_weekly_loss' => 'decimal:2',
        'max_monthly_loss' => 'decimal:2',
        'max_win_cap' => 'decimal:2',
        'cool_down_minutes' => 'integer',
        'session_timeout_minutes' => 'integer',
        'enable_free_spins' => 'boolean',
        'enable_bonus_buy' => 'boolean',
        'bonus_buy_price' => 'decimal:2',
    ];

    public function symbols()
    {
        return $this->belongsToMany(LotterySymbol::class, 'lottery_payouts')
                    ->withPivot('count', 'payout_multiplier')
                    ->withTimestamps();
    }

    public function bonusSymbol()
    {
        return $this->belongsTo(LotterySymbol::class, 'bonus_symbol_id');
    }

    public function spins()
    {
        return $this->hasMany(LotterySpin::class, 'lottery_game_id');
    }

    public function getCurrentJackpot()
    {
        return Cache::remember("lottery_jackpot_{$this->id}", 10, fn() => $this->progressive_jackpot);
    }

    public function updateJackpot($newAmount)
    {
        $this->progressive_jackpot = $newAmount;
        $this->save();
        Cache::forget("lottery_jackpot_{$this->id}");
    }

    public function contributeToJackpot($betAmount)
    {
        $contribution = $betAmount * ($this->jackpot_contribution_rate / 100);
        $this->increment('progressive_jackpot', $contribution);
        Cache::forget("lottery_jackpot_{$this->id}");
        return $contribution;
    }

    public function getRtpForUser(User $user)
    {
        if ($user->is_admin) return $this->promo_rtp;
        if ($user->kyc_level >= 2) return $this->vip_rtp;
        return $this->base_rtp;
    }

    public function adjustWithRtp($winAmount, $betAmount, ?User $user)
    {
        if (!$user) return $winAmount;
        $targetRtp = $this->getRtpForUser($user) / 100;
        return round($winAmount * $targetRtp, 2);
    }

    public function getPaylines()
    {
        return $this->paylines ?? $this->getDefaultPaylines();
    }

    protected function getDefaultPaylines()
    {
        return [
            [[0,0],[1,0],[2,0]], [[0,1],[1,1],[2,1]], [[0,2],[1,2],[2,2]],
            [[0,0],[1,1],[2,2]], [[0,2],[1,1],[2,0]],
        ];
    }

    public function isWithinLimits(User $user, $betAmount)
    {
        $todayLoss = $this->getUserLossSince($user, now()->startOfDay());
        $weeklyLoss = $this->getUserLossSince($user, now()->startOfWeek());
        $monthlyLoss = $this->getUserLossSince($user, now()->startOfMonth());
        if ($this->max_daily_loss && ($todayLoss + $betAmount) > $this->max_daily_loss) return false;
        if ($this->max_weekly_loss && ($weeklyLoss + $betAmount) > $this->max_weekly_loss) return false;
        if ($this->max_monthly_loss && ($monthlyLoss + $betAmount) > $this->max_monthly_loss) return false;
        return true;
    }

    protected function getUserLossSince(User $user, $startDate)
    {
        return LotterySpin::where('user_id', $user->id)->where('created_at', '>=', $startDate)
            ->where('win_amount', '<', 'bet_amount')->sum('bet_amount');
    }

    public function applyWinCap($winAmount)
    {
        if ($this->max_win_cap && $winAmount > $this->max_win_cap) return $this->max_win_cap;
        return $winAmount;
    }

    public function isInCooldown(User $user)
    {
        if (!$this->cool_down_minutes) return false;
        $lastSpin = LotterySpin::where('user_id', $user->id)->latest()->first();
        if (!$lastSpin) return false;
        return $lastSpin->created_at->diffInMinutes(now()) < $this->cool_down_minutes;
    }

    public function isSessionExpired(User $user)
    {
        if (!$this->session_timeout_minutes) return false;
        $lastSpin = LotterySpin::where('user_id', $user->id)->latest()->first();
        if (!$lastSpin) return false;
        return $lastSpin->created_at->diffInMinutes(now()) > $this->session_timeout_minutes;
    }

    public function getBonusBuyPrice()
    {
        return $this->enable_bonus_buy ? ($this->bonus_buy_price ?? 100) : null;
    }

    public function getVolatility()
    {
        return $this->volatility ?? 'medium';
    }

    public function adjustPayoutByVolatility($baseMultiplier)
    {
        $factor = match($this->getVolatility()) {
            'low' => 0.7, 'medium' => 1.0, 'high' => 1.5, 'extreme' => 2.0, default => 1.0,
        };
        return round($baseMultiplier * $factor, 2);
    }

    public function getRealRtp()
    {
        $totalBet = $this->spins()->sum('bet_amount');
        $totalWin = $this->spins()->sum('win_amount');
        if ($totalBet == 0) return $this->base_rtp;
        return round(($totalWin / $totalBet) * 100, 2);
    }

    public function getTotalJackpotContribution()
    {
        return $this->spins()->sum('tax_contribution');
    }
}
