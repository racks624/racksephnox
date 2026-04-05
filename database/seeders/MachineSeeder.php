<?php

namespace Database\Seeders;

use App\Models\Machine;
use Illuminate\Database\Seeder;

class MachineSeeder extends Seeder
{
    public function run()
    {
        $machines = [
            ['name' => 'RX1', 'code' => 'rx1', 'vip1_start_amount' => 200,   'vip2_start_amount' => 324,   'vip3_start_amount' => 524],
            ['name' => 'RX2', 'code' => 'rx2', 'vip1_start_amount' => 1200,  'vip2_start_amount' => 1942,  'vip3_start_amount' => 3142],
            ['name' => 'RX3', 'code' => 'rx3', 'vip1_start_amount' => 2500,  'vip2_start_amount' => 4045,  'vip3_start_amount' => 6545],
            ['name' => 'RX4', 'code' => 'rx4', 'vip1_start_amount' => 3500,  'vip2_start_amount' => 5663,  'vip3_start_amount' => 9163],
            ['name' => 'RX5', 'code' => 'rx5', 'vip1_start_amount' => 4400,  'vip2_start_amount' => 7120,  'vip3_start_amount' => 11520],
            ['name' => 'RX6', 'code' => 'rx6', 'vip1_start_amount' => 5300,  'vip2_start_amount' => 8576,  'vip3_start_amount' => 13877],
        ];

        foreach ($machines as $data) {
            Machine::create(array_merge($data, [
                'duration_days' => 14,
                'growth_rate' => 25.00,
                'is_active' => true,
            ]));
        }
    }
}
