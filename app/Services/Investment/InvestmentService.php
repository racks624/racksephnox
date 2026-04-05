<?php

namespace App\Services\Investment;

use App\Models\Investment;
use App\Models\InvestmentPlan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InvestmentService
{
    /**
     * Create a new investment.
     */
    public function create(User $user, InvestmentPlan $plan, $amount, $autoReinvest = false, $compoundType = 'daily_payout')
    {
        if ($amount < $plan->min_amount || $amount > $plan->max_amount) {
            throw new \InvalidArgumentException('Amount outside allowed range');
        }
        if ($user->wallet->balance < $amount) {
            throw new \InvalidArgumentException('Insufficient balance');
        }

        return DB::transaction(function () use ($user, $plan, $amount, $autoReinvest, $compoundType) {
            // Debit wallet
            $user->wallet->debit($amount, 'Investment in ' . $plan->name);

            $dailyProfit = $plan->getDailyProfit($amount);
            $totalProfit = $dailyProfit * $plan->duration_days;
            $endDate = now()->addDays($plan->duration_days);

            $investment = Investment::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'amount' => $amount,
                'daily_profit' => $dailyProfit,
                'total_projected_profit' => $totalProfit,
                'remaining_days' => $plan->duration_days,
                'status' => Investment::STATUS_ACTIVE,
                'start_date' => now(),
                'end_date' => $endDate,
                'last_accrued_at' => now(),
                'auto_reinvest' => $autoReinvest,
                'compound_type' => $compoundType,
                'early_withdrawal_penalty' => $plan->early_withdrawal_penalty,
                'max_cycles' => $plan->max_reinvestment_cycles ?? 1,
                'current_cycle' => 1,
            ]);

            return $investment;
        });
    }

    /**
     * Accrue daily profit for an investment.
     */
    public function accrueProfit(Investment $investment)
    {
        if ($investment->status !== Investment::STATUS_ACTIVE) {
            return false;
        }

        return DB::transaction(function () use ($investment) {
            $profit = $investment->daily_profit;

            if ($investment->compound_type === 'reinvest') {
                // Reinvest: add to investment amount (increase principal)
                $investment->increment('amount', $profit);
                $investment->increment('total_projected_profit', $profit);
                // Recalculate daily profit based on new amount
                $newDailyProfit = $investment->plan->getDailyProfit($investment->amount);
                $investment->daily_profit = $newDailyProfit;
                $investment->save();
            } else {
                // Daily payout: credit to wallet
                $investment->user->wallet->credit($profit, 'Daily profit from ' . $investment->plan->name, 'interest');
            }

            $investment->remaining_days--;
            $investment->last_accrued_at = now();

            // Check if completed
            if ($investment->remaining_days <= 0) {
                $investment->status = Investment::STATUS_COMPLETED;
                // Auto‑reinvest logic if enabled and cycles remain
                if ($investment->auto_reinvest && $investment->current_cycle < $investment->max_cycles) {
                    $this->autoReinvest($investment);
                }
            }
            $investment->save();

            return true;
        });
    }

    /**
     * Auto‑reinvest upon maturity.
     */
    public function autoReinvest(Investment $oldInvestment)
    {
        // Create a new investment with the same plan and the matured amount (principal + profit)
        $newAmount = $oldInvestment->amount + $oldInvestment->total_projected_profit;
        $newCycle = $oldInvestment->current_cycle + 1;

        $newInvestment = $this->create(
            $oldInvestment->user,
            $oldInvestment->plan,
            $newAmount,
            $oldInvestment->auto_reinvest,
            $oldInvestment->compound_type
        );
        $newInvestment->current_cycle = $newCycle;
        $newInvestment->save();

        // Optionally link the old investment to the new one (not required)
        return $newInvestment;
    }
}
