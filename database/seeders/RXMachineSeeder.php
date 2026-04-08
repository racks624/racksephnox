<?php

namespace Database\Seeders;

use App\Models\Machine;
use Illuminate\Database\Seeder;

class RXMachineSeeder extends Seeder
{
    public function run()
    {
        $phi = Machine::PHI;
        
        $machines = [
            [
                'code' => 'RX1',
                'name' => 'RX1 – Aurora Machine',
                'description' => 'Entry‑level divine portal, perfect for first‑time investors. Low risk, steady returns.',
                'vip1_start_amount' => 5300,
                'duration_days' => 14,
                'growth_rate' => 25.0,
                'risk_profile' => 'Low',
                'icon' => 'fa-feather-alt',
                'color' => 'from-blue-400 to-cyan-400',
                'min_daily_profit' => 94.64,
                'max_daily_profit' => 247.77,
                'referral_bonus_rate' => 5.0,
                'early_withdrawal_penalty' => 10.0,
                'features' => ['Instant daily profit', 'Low risk', 'Beginner friendly']
            ],
            [
                'code' => 'RX2',
                'name' => 'RX2 – Nova Machine',
                'description' => 'Intermediate growth portal. Balanced risk with amplified returns.',
                'vip1_start_amount' => 11000,
                'duration_days' => 14,
                'growth_rate' => 26.0,
                'risk_profile' => 'Low‑Medium',
                'icon' => 'fa-star',
                'color' => 'from-green-400 to-emerald-400',
                'referral_bonus_rate' => 5.5,
                'early_withdrawal_penalty' => 12.0,
                'features' => ['Balanced risk', 'Higher returns', 'VIP benefits']
            ],
            [
                'code' => 'RX3',
                'name' => 'RX3 – Prism Machine',
                'description' => 'Advanced high‑yield portal. Medium risk with exceptional gains.',
                'vip1_start_amount' => 22000,
                'duration_days' => 14,
                'growth_rate' => 27.0,
                'risk_profile' => 'Medium',
                'icon' => 'fa-gem',
                'color' => 'from-purple-400 to-pink-400',
                'referral_bonus_rate' => 6.0,
                'early_withdrawal_penalty' => 15.0,
                'features' => ['High yield', 'Medium risk', 'Advanced analytics']
            ],
            [
                'code' => 'RX4',
                'name' => 'RX4 – Eclipse Machine',
                'description' => 'Premium wealth portal. Medium‑high risk for experienced investors.',
                'vip1_start_amount' => 53000,
                'duration_days' => 14,
                'growth_rate' => 28.0,
                'risk_profile' => 'Medium‑High',
                'icon' => 'fa-moon',
                'color' => 'from-indigo-400 to-purple-400',
                'referral_bonus_rate' => 6.5,
                'early_withdrawal_penalty' => 18.0,
                'features' => ['Premium returns', 'Wealth acceleration', 'Priority support']
            ],
            [
                'code' => 'RX5',
                'name' => 'RX5 – Quantum Machine',
                'description' => 'Elite high‑frequency portal. High risk, exponential returns.',
                'vip1_start_amount' => 110000,
                'duration_days' => 14,
                'growth_rate' => 30.0,
                'risk_profile' => 'High',
                'icon' => 'fa-atom',
                'color' => 'from-orange-400 to-red-400',
                'referral_bonus_rate' => 7.0,
                'early_withdrawal_penalty' => 20.0,
                'features' => ['Exponential growth', 'High risk high reward', 'VIP support']
            ],
            [
                'code' => 'RX6',
                'name' => 'RX6 – Infinity Machine',
                'description' => 'The ultimate sacred investment portal. Very high risk, divine returns.',
                'vip1_start_amount' => 220000,
                'duration_days' => 14,
                'growth_rate' => 35.0,
                'risk_profile' => 'Very High',
                'icon' => 'fa-infinity',
                'color' => 'from-amber-400 to-yellow-400',
                'referral_bonus_rate' => 8.0,
                'early_withdrawal_penalty' => 25.0,
                'features' => ['Maximum returns', 'Divine algorithm', 'Exclusive access']
            ],
        ];

        foreach ($machines as $data) {
            Machine::updateOrCreate(['code' => $data['code']], $data);
        }
    }
}
