<?php

namespace Database\Seeders;

use App\Models\LotteryBonusWheel;
use Illuminate\Database\Seeder;

class LotteryBonusWheelSeeder extends Seeder
{
    public function run()
    {
        $segments = [
            ['type' => 'kes', 'value' => 10],
            ['type' => 'kes', 'value' => 20],
            ['type' => 'free_spin', 'value' => 1],
            ['type' => 'kes', 'value' => 50],
            ['type' => 'free_spin', 'value' => 2],
            ['type' => 'kes', 'value' => 100],
            ['type' => 'free_spin', 'value' => 5],
            ['type' => 'kes', 'value' => 500],
        ];
        LotteryBonusWheel::updateOrCreate(['name' => 'Daily Bonus Wheel'], [
            'segments' => $segments,
            'is_active' => true,
        ]);
    }
}
