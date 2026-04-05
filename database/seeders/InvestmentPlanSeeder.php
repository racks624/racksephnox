<?php

namespace Database\Seeders;

use App\Models\InvestmentPlan;
use Illuminate\Database\Seeder;

class InvestmentPlanSeeder extends Seeder
{
    public function run()
    {
        InvestmentPlan::create([
            'name' => 'Basic Plan',
            'description' => 'Start with small investments, steady daily returns.',
            'min_amount' => 1000,
            'max_amount' => 50000,
            'daily_interest_rate' => 1.5,
            'duration_days' => 30,
            'is_active' => true,
        ]);

        InvestmentPlan::create([
            'name' => 'Premium Plan',
            'description' => 'Higher returns for larger investments.',
            'min_amount' => 50000,
            'max_amount' => 500000,
            'daily_interest_rate' => 2.0,
            'duration_days' => 60,
            'is_active' => true,
        ]);

        InvestmentPlan::create([
            'name' => 'VIP Plan',
            'description' => 'Exclusive plan with maximum benefits.',
            'min_amount' => 500000,
            'max_amount' => 5000000,
            'daily_interest_rate' => 2.5,
            'duration_days' => 90,
            'is_active' => true,
        ]);
    }
}
