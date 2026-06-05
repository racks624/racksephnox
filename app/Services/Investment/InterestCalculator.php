<?php

namespace App\Services\Investment;

use App\Models\Investment;
use App\Models\InvestmentPlan;

class InterestCalculator
{
    /**
     * Calculate daily profit for an investment
     */
    public function calculateDailyProfit($amount, InvestmentPlan $plan)
    {
        return round($amount * ($plan->daily_interest_rate / 100), 2);
    }

    /**
     * Calculate total projected profit for an investment
     */
    public function calculateTotalProfit($amount, InvestmentPlan $plan)
    {
        $daily = $this->calculateDailyProfit($amount, $plan);
        return round($daily * $plan->duration_days, 2);
    }

    /**
     * Calculate end date based on duration
     */
    public function calculateEndDate($startDate, $durationDays)
    {
        return $startDate->copy()->addDays($durationDays);
    }
}
