<?php
namespace Database\Seeders;
use App\Models\LotteryBonusWheel;
use Illuminate\Database\Seeder;
class LotteryBonusWheelSeeder extends Seeder
{
    public function run()
    {
        LotteryBonusWheel::updateOrCreate(['name' => 'Daily Divine Wheel'], [
            'segments' => [
                ['type' => 'kes', 'value' => 10], ['type' => 'kes', 'value' => 20], ['type' => 'kes', 'value' => 50],
                ['type' => 'free_spin', 'value' => 1], ['type' => 'kes', 'value' => 5], ['type' => 'kes', 'value' => 100],
                ['type' => 'free_spin', 'value' => 2], ['type' => 'kes', 'value' => 0], ['type' => 'kes', 'value' => 30],
            ],
            'is_active' => true,
        ]);
    }
}
