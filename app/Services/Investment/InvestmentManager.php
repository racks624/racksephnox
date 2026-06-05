<?php

namespace App\Services\Investment;

use App\Models\Investment;
use App\Models\InvestmentPlan;
use App\Models\User;
use App\Events\InvestmentCreated;
use Illuminate\Support\Facades\DB;

class InvestmentManager
{
    protected $calculator;

    public function __construct(InterestCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * Create a new investment
     */
    public function create(User $user, InvestmentPlan $plan, $amount)
    {
        if ($amount < $plan->min_amount || $amount > $plan->max_amount) {
            throw new \InvalidArgumentException('Amount outside allowed range');
        }

        if ($user->wallet->balance < $amount) {
            throw new \InvalidArgumentException('Insufficient balance');
        }

        return DB::transaction(function () use ($user, $plan, $amount) {
            // Debit user wallet
            $user->wallet->debit($amount, 'Investment in ' . $plan->name);

            $dailyProfit = $this->calculator->calculateDailyProfit($amount, $plan);
            $totalProfit = $this->calculator->calculateTotalProfit($amount, $plan);
            $endDate = $this->calculator->calculateEndDate(now(), $plan->duration_days);

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
            ]);

            event(new InvestmentCreated($investment));
            $user->notify(new AppNotificationsInvestmentCreated($investment));
            $user->notify(new AppNotificationsInvestmentCreated($investment));

            return $investment;
        });
    }
}
