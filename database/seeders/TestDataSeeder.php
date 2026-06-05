<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Investment;
use App\Models\Transaction;
use App\Models\InvestmentPlan;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        $user = User::where('email', 'spiralsourcetechlaws@gmail.com')->first();
        if (!$user) return;

        // Add some transactions
        for ($i = 1; $i <= 5; $i++) {
            $user->wallet->credit(1000 * $i, 'Test deposit ' . $i);
        }

        // Create an investment
        $plan = InvestmentPlan::first();
        if ($plan) {
            app(\App\Services\Investment\InvestmentManager::class)
                ->create($user, $plan, 5000);
        }

        $this->command->info('Test data seeded.');
    }
}
