<?php

namespace Database\Seeders;

use App\Models\Machine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RXMachineSeeder extends Seeder
{
    public function run()
    {
        $phi = 1.61803398875;
        
        $machines = [
            [
                'code' => 'RX1',
                'name' => 'RX1 – Aurora Machine',
                'vip1_start_amount' => 5300,
                'vip2_start_amount' => round(5300 * $phi, 2),
                'vip3_start_amount' => round(5300 * pow($phi, 2), 2),
                'duration_days' => 14,
                'growth_rate' => 25.0,
                'is_active' => true,
            ],
            [
                'code' => 'RX2',
                'name' => 'RX2 – Nova Machine',
                'vip1_start_amount' => 11000,
                'vip2_start_amount' => round(11000 * $phi, 2),
                'vip3_start_amount' => round(11000 * pow($phi, 2), 2),
                'duration_days' => 14,
                'growth_rate' => 26.0,
                'is_active' => true,
            ],
            [
                'code' => 'RX3',
                'name' => 'RX3 – Prism Machine',
                'vip1_start_amount' => 22000,
                'vip2_start_amount' => round(22000 * $phi, 2),
                'vip3_start_amount' => round(22000 * pow($phi, 2), 2),
                'duration_days' => 14,
                'growth_rate' => 27.0,
                'is_active' => true,
            ],
            [
                'code' => 'RX4',
                'name' => 'RX4 – Eclipse Machine',
                'vip1_start_amount' => 53000,
                'vip2_start_amount' => round(53000 * $phi, 2),
                'vip3_start_amount' => round(53000 * pow($phi, 2), 2),
                'duration_days' => 14,
                'growth_rate' => 28.0,
                'is_active' => true,
            ],
            [
                'code' => 'RX5',
                'name' => 'RX5 – Quantum Machine',
                'vip1_start_amount' => 110000,
                'vip2_start_amount' => round(110000 * $phi, 2),
                'vip3_start_amount' => round(110000 * pow($phi, 2), 2),
                'duration_days' => 14,
                'growth_rate' => 30.0,
                'is_active' => true,
            ],
            [
                'code' => 'RX6',
                'name' => 'RX6 – Infinity Machine',
                'vip1_start_amount' => 220000,
                'vip2_start_amount' => round(220000 * $phi, 2),
                'vip3_start_amount' => round(220000 * pow($phi, 2), 2),
                'duration_days' => 14,
                'growth_rate' => 35.0,
                'is_active' => true,
            ],
        ];

        // Use updateOrCreate instead of truncate (preserves foreign keys)
        foreach ($machines as $data) {
            Machine::updateOrCreate(['code' => $data['code']], $data);
            $this->command->info("✅ Created/Updated: {$data['name']}");
            $this->command->line("   VIP 1: KES " . number_format($data['vip1_start_amount'], 2));
            $this->command->line("   VIP 2: KES " . number_format($data['vip2_start_amount'], 2));
            $this->command->line("   VIP 3: KES " . number_format($data['vip3_start_amount'], 2));
            $this->command->line("");
        }
        
        $this->command->info("✨ Total machines: " . Machine::count());
    }
}
