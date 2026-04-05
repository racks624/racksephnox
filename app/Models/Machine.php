<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'vip1_start_amount', 'vip2_start_amount', 'vip3_start_amount',
        'duration_days', 'growth_rate', 'is_active'
    ];

    protected $casts = [
        'vip1_start_amount' => 'decimal:2',
        'vip2_start_amount' => 'decimal:2',
        'vip3_start_amount' => 'decimal:2',
        'growth_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function investments()
    {
        return $this->hasMany(MachineInvestment::class);
    }

    /**
     * Get start amount for a given VIP level.
     */
    public function getStartAmountForVip($level)
    {
        return match ((int) $level) {
            1 => $this->vip1_start_amount,
            2 => $this->vip2_start_amount,
            3 => $this->vip3_start_amount,
            default => 0,
        };
    }

    /**
     * Calculate daily profit for an investment amount.
     */
    public function getDailyProfit($amount)
    {
        $totalReturn = $amount * (1 + $this->growth_rate / 100);
        return round($totalReturn / $this->duration_days, 2);
    }

    /**
     * Calculate total return for an investment amount.
     */
    public function getTotalReturn($amount)
    {
        return round($amount * (1 + $this->growth_rate / 100), 2);
    }

    /**
     * Get VIP amounts based on golden ratio (φ).
     * VIP2 = VIP1 * φ, VIP3 = VIP2 * φ.
     */
    public function getVIPAmounts()
    {
        $phi = 1.61803398875;
        $vip1 = $this->vip1_start_amount;
        return [
            1 => $vip1,
            2 => round($vip1 * $phi, 2),
            3 => round($vip1 * pow($phi, 2), 2),
        ];
    }
}
