<?php
namespace Database\Seeders;

use App\Models\InvestmentPlan;
use Illuminate\Database\Seeder;

class InvestmentPlanSeeder extends Seeder
{
    public function run()
    {
        $plans = [
            ['name' => 'Starter Plan', 'min_amount' => 1000, 'max_amount' => 10000, 'daily_interest_rate' => 1.5, 'duration_days' => 30],
            ['name' => 'Growth Plan', 'min_amount' => 10001, 'max_amount' => 50000, 'daily_interest_rate' => 2.0, 'duration_days' => 45],
            ['name' => 'Premium Plan', 'min_amount' => 50001, 'max_amount' => 200000, 'daily_interest_rate' => 2.5, 'duration_days' => 60],
        ];
        foreach ($plans as $plan) {
            $plan['is_active'] = true;
            $plan['allow_early_withdrawal'] = true;
            $plan['early_withdrawal_penalty'] = 20;
            InvestmentPlan::create($plan);
        }
    }
}
