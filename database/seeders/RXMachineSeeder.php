<?php

namespace Database\Seeders;

use App\Models\Machine;
use Illuminate\Database\Seeder;

class RXMachineSeeder extends Seeder
{
    public function run()
    {
        $machines = [
            // RX0 (Origin)
            [
                'code' => 'RX0',
                'name' => 'RX0 -- Origin Machine',
                'vip1_start_amount' => 200,
                'vip2_start_amount' => 300,
                'vip3_start_amount' => 500,
                'duration_days' => 14,
                'growth_rate' => 88.0,
                'is_active' => true,
                'risk_profile' => 'Low',
                'icon' => 'fa-seedling',
                'color' => 'from-green-400 to-green-600',
                'early_withdrawal_penalty' => 20,
                'referral_bonus_rate' => 5,
            ],
            // RX1
            [
                'code' => 'RX1',
                'name' => 'RX1 -- Aurora Machine',
                'vip1_start_amount' => 800,
                'vip2_start_amount' => 1300,
                'vip3_start_amount' => 2100,
                'duration_days' => 14,
                'growth_rate' => 88.0,
                'is_active' => true,
                'risk_profile' => 'Medium-Low',
                'icon' => 'fa-cloud-sun',
                'color' => 'from-cyan-400 to-blue-400',
                'early_withdrawal_penalty' => 20,
                'referral_bonus_rate' => 5,
            ],
            // RX2
            [
                'code' => 'RX2',
                'name' => 'RX2 -- Nova Machine',
                'vip1_start_amount' => 2500,
                'vip2_start_amount' => 4000,
                'vip3_start_amount' => 6500,
                'duration_days' => 14,
                'growth_rate' => 88.0,
                'is_active' => true,
                'risk_profile' => 'Medium',
                'icon' => 'fa-star',
                'color' => 'from-yellow-400 to-orange-400',
                'early_withdrawal_penalty' => 20,
                'referral_bonus_rate' => 5,
            ],
            // RX3
            [
                'code' => 'RX3',
                'name' => 'RX3 -- Prism Machine',
                'vip1_start_amount' => 4000,
                'vip2_start_amount' => 6500,
                'vip3_start_amount' => 10500,
                'duration_days' => 14,
                'growth_rate' => 88.0,
                'is_active' => true,
                'risk_profile' => 'Medium',
                'icon' => 'fa-gem',
                'color' => 'from-purple-400 to-pink-400',
                'early_withdrawal_penalty' => 20,
                'referral_bonus_rate' => 5,
            ],
            // RX4
            [
                'code' => 'RX4',
                'name' => 'RX4 -- Eclipse Machine',
                'vip1_start_amount' => 8000,
                'vip2_start_amount' => 13000,
                'vip3_start_amount' => 21000,
                'duration_days' => 14,
                'growth_rate' => 88.0,
                'is_active' => true,
                'risk_profile' => 'Medium-High',
                'icon' => 'fa-moon',
                'color' => 'from-indigo-400 to-purple-400',
                'early_withdrawal_penalty' => 20,
                'referral_bonus_rate' => 5,
            ],
            // RX5
            [
                'code' => 'RX5',
                'name' => 'RX5 -- Quantum Machine',
                'vip1_start_amount' => 12000,
                'vip2_start_amount' => 19500,
                'vip3_start_amount' => 31500,
                'duration_days' => 14,
                'growth_rate' => 88.0,
                'is_active' => true,
                'risk_profile' => 'High',
                'icon' => 'fa-atom',
                'color' => 'from-blue-500 to-indigo-600',
                'early_withdrawal_penalty' => 20,
                'referral_bonus_rate' => 5,
            ],
            // RX6
            [
                'code' => 'RX6',
                'name' => 'RX6 -- Infinity Machine',
                'vip1_start_amount' => 25000,
                'vip2_start_amount' => 40500,
                'vip3_start_amount' => 65500,
                'duration_days' => 14,
                'growth_rate' => 88.0,
                'is_active' => true,
                'risk_profile' => 'Very High',
                'icon' => 'fa-infinity',
                'color' => 'from-gold-400 to-amber-600',
                'early_withdrawal_penalty' => 20,
                'referral_bonus_rate' => 5,
            ],
        ];

        foreach ($machines as $data) {
            Machine::updateOrCreate(['code' => $data['code']], $data);
            $this->command->info("✅ Created/Updated: {$data['name']}");
        }
        $this->command->info("✨ Total machines: " . Machine::count());
    }
}
