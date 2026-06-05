<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'min_amount', 'max_amount',
        'daily_interest_rate', 'duration_days', 'is_active',
        'allow_auto_reinvest', 'allow_early_withdrawal', 'early_withdrawal_penalty',
        'max_reinvestment_cycles', 'is_infinite'
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'daily_interest_rate' => 'decimal:4',
        'is_active' => 'boolean',
        'allow_auto_reinvest' => 'boolean',
        'allow_early_withdrawal' => 'boolean',
        'early_withdrawal_penalty' => 'decimal:2',
        'is_infinite' => 'boolean',
    ];

    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    /**
     * Calculate daily profit for a given amount.
     */
    public function getDailyProfit($amount)
    {
        return round($amount * ($this->daily_interest_rate / 100), 2);
    }

    /**
     * Calculate total return for a given amount after full duration.
     */
    public function getTotalReturn($amount)
    {
        return round($amount * (1 + $this->daily_interest_rate / 100 * $this->duration_days), 2);
    }

    /**
     * Get VIP amounts based on golden ratio (φ) using the plan's min_amount as base.
     */
    public function getVIPAmounts()
    {
        $phi = 1.61803398875;
        $vip1 = $this->min_amount;
        return [
            1 => $vip1,
            2 => round($vip1 * $phi, 2),
            3 => round($vip1 * pow($phi, 2), 2),
        ];
    }

    /**
     * Get compound profit for a given amount after a number of days.
     */
    public function getCompoundProfit($amount, $days)
    {
        $dailyRate = $this->daily_interest_rate / 100;
        $total = $amount * pow(1 + $dailyRate, $days);
        return round($total, 2);
    }
}
