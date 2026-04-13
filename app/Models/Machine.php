<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Machine extends Model
{
    use HasFactory;

    // Sacred Constants
    const PHI = 1.618033988749895;
    const LAMBDA = 1.272019649514069;
    const PI = 3.141592653589793;
    const EULER = 2.718281828459045;

    // Risk Tiers
    const RISK_LOW = 'low';
    const RISK_MEDIUM_LOW = 'medium-low';
    const RISK_MEDIUM = 'medium';
    const RISK_MEDIUM_HIGH = 'medium-high';
    const RISK_HIGH = 'high';
    const RISK_VERY_HIGH = 'very-high';

    // VIP Levels
    const VIP_BRONZE = 1;
    const VIP_SILVER = 2;
    const VIP_GOLD = 3;
    const VIP_PLATINUM = 4;
    const VIP_DIAMOND = 5;
    const VIP_SACRED = 6;

    protected $fillable = [
        'code', 'name', 'description', 'vip1_start_amount', 'vip2_start_amount', 'vip3_start_amount',
        'duration_days', 'growth_rate', 'is_active', 'icon', 'color', 'risk_profile',
        'min_daily_profit', 'max_daily_profit', 'total_invested_limit',
        'referral_bonus_rate', 'early_withdrawal_penalty', 'features',
        'compound_frequency', 'min_withdrawal', 'max_withdrawal',
        'bonus_multiplier', 'staking_reward', 'tier_multiplier'
    ];

    protected $casts = [
        'vip1_start_amount' => 'decimal:2',
        'vip2_start_amount' => 'decimal:2',
        'vip3_start_amount' => 'decimal:2',
        'growth_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'min_daily_profit' => 'decimal:2',
        'max_daily_profit' => 'decimal:2',
        'total_invested_limit' => 'decimal:2',
        'referral_bonus_rate' => 'decimal:2',
        'early_withdrawal_penalty' => 'decimal:2',
        'features' => 'array',
        'compound_frequency' => 'integer',
        'min_withdrawal' => 'decimal:2',
        'max_withdrawal' => 'decimal:2',
        'bonus_multiplier' => 'decimal:2',
        'staking_reward' => 'decimal:2',
        'tier_multiplier' => 'decimal:2'
    ];

    /**
     * Advanced VIP amount calculation with Fibonacci scaling
     */
    public function getVIPAmounts(): array
    {
        $phi = self::PHI;
        $base = $this->vip1_start_amount;
        
        return [
            1 => $base,
            2 => round($base * $phi, 2),
            3 => round($base * pow($phi, 2), 2),
            4 => round($base * pow($phi, 3), 2),
            5 => round($base * pow($phi, 4), 2),
            6 => round($base * pow($phi, 5), 2),
        ];
    }

    /**
     * Calculate compound interest with Euler's number
     */
    public function getCompoundReturn(float $amount, int $days): float
    {
        $rate = $this->growth_rate / 100;
        $compoundRate = exp($rate * $days / $this->duration_days);
        return round($amount * $compoundRate, 2);
    }

    /**
     * Calculate daily profit with tier multiplier
     */
    public function getDailyProfit(float $amount, int $vipLevel = 1): float
    {
        $multiplier = 1 + (($vipLevel - 1) * 0.05);
        $totalReturn = $amount * (1 + ($this->growth_rate * $multiplier / 100));
        return round($totalReturn / $this->duration_days, 2);
    }

    /**
     * Calculate total return with bonus multiplier
     */
    public function getTotalReturn(float $amount, int $vipLevel = 1): float
    {
        $multiplier = 1 + (($vipLevel - 1) * 0.05);
        $bonusMultiplier = $this->bonus_multiplier ?? 1;
        return round($amount * (1 + ($this->growth_rate * $multiplier / 100)) * $bonusMultiplier, 2);
    }

    /**
     * Get start amount for a given VIP level
     */
    public function getStartAmountForVip($level)
    {
        $amounts = $this->getVIPAmounts();
        return $amounts[$level] ?? 0;
    }

    /**
     * Get complete VIP details with enterprise features (cached)
     */
    public function getVIPDetails(): array
    {
        return Cache::remember("machine_vip_details_{$this->id}", 3600, function () {
            $vips = [];
            $amounts = $this->getVIPAmounts();
            $vipNames = ['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Sacred'];
            $vipColors = ['#cd7f32', '#c0c0c0', '#ffd700', '#e5e4e2', '#b9f2ff', '#d4af37'];
            $vipIcons = ['fa-crown', 'fa-gem', 'fa-star', 'fa-infinity', 'fa-dragon', 'fa-sun'];
            
            for ($level = 1; $level <= 6; $level++) {
                $amount = $amounts[$level] ?? $this->vip1_start_amount * pow(self::PHI, $level - 1);
                $dailyProfit = $this->getDailyProfit($amount, $level);
                $totalReturn = $this->getTotalReturn($amount, $level);
                $totalProfit = $totalReturn - $amount;
                $compoundReturn = $this->getCompoundReturn($amount, $this->duration_days);
                
                $vips[$level] = [
                    'level'          => $level,
                    'name'           => $vipNames[$level - 1],
                    'color'          => $vipColors[$level - 1],
                    'icon'           => $vipIcons[$level - 1],
                    'phi_power'      => str_repeat('¹', $level),
                    'amount'         => $amount,
                    'daily_profit'   => $dailyProfit,
                    'total_return'   => $totalReturn,
                    'total_profit'   => $totalProfit,
                    'compound_return'=> $compoundReturn,
                    'daily_rate'     => round(($dailyProfit / $amount) * 100, 4),
                    'roi'            => round(($totalProfit / $amount) * 100, 2),
                    'apy'            => round((pow(1 + $dailyProfit / $amount, 365) - 1) * 100, 2),
                    'multiplier'     => 1 + (($level - 1) * 0.05),
                    'staking_reward' => $this->staking_reward * $level ?? 0,
                    'referral_bonus' => $this->referral_bonus_rate + ($level * 0.5),
                ];
            }
            return $vips;
        });
    }

    /**
     * Get machine statistics (cached)
     */
    public function getStatistics(): array
    {
        return Cache::remember("machine_statistics_{$this->id}", 300, function () {
            $activeInvestments = $this->activeInvestments();
            return [
                'total_investors'         => $this->investments()->distinct('user_id')->count('user_id'),
                'total_invested'          => $this->investments()->sum('amount'),
                'total_paid_out'          => $this->investments()->sum('total_return'),
                'total_profit'            => $this->investments()->sum('total_return') - $this->investments()->sum('amount'),
                'active_investments'      => $activeInvestments->count(),
                'completion_rate'         => $this->getCompletionRate(),
                'avg_investment'          => $activeInvestments->avg('amount') ?? 0,
                'total_daily_payout'      => $activeInvestments->sum('daily_profit'),
                'estimated_monthly_payout'=> $activeInvestments->sum('daily_profit') * 30,
                'roi_percentage'          => $this->getRoiPercentage(),
                'popular_vip_level'       => $this->getPopularVIPLevel(),
            ];
        });
    }

    /**
     * Get completion rate
     */
    private function getCompletionRate(): float
    {
        $total = $this->investments()->count();
        if ($total === 0) return 0;
        $completed = $this->investments()->where('status', 'completed')->count();
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Get ROI percentage
     */
    private function getRoiPercentage(): float
    {
        $totalInvested = $this->investments()->sum('amount');
        if ($totalInvested === 0) return 0;
        $totalProfit = $this->investments()->sum('total_return') - $totalInvested;
        return round(($totalProfit / $totalInvested) * 100, 2);
    }

    /**
     * Get popular VIP level
     */
    private function getPopularVIPLevel(): int
    {
        $counts = [
            1 => $this->investments()->where('vip_level', 1)->count(),
            2 => $this->investments()->where('vip_level', 2)->count(),
            3 => $this->investments()->where('vip_level', 3)->count(),
        ];
        $maxCount = max($counts);
        if ($maxCount === 0) return 1;
        return array_keys($counts, $maxCount)[0] ?? 1;
    }

    /**
     * Check if machine can accept more investment
     */
    public function canAcceptInvestment(float $amount): bool
    {
        if (!$this->total_invested_limit) return true;
        $currentTotal = $this->investments()->where('status', 'active')->sum('amount');
        return ($currentTotal + $amount) <= $this->total_invested_limit;
    }

    // Relationships
    public function investments()
    {
        return $this->hasMany(MachineInvestment::class);
    }

    public function activeInvestments()
    {
        return $this->investments()->where('status', 'active');
    }
}
