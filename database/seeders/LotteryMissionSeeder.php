<?php
namespace Database\Seeders;
use App\Models\LotteryMission;
use Illuminate\Database\Seeder;
class LotteryMissionSeeder extends Seeder
{
    public function run()
    {
        $missions = [
            ['name' => 'Spin 10 Times', 'description' => 'Spin the slot 10 times', 'type' => 'spins', 'target' => 10, 'reward_type' => 'free_spin', 'reward_free_spins' => 2, 'is_daily' => true],
            ['name' => 'Win 50 KES', 'description' => 'Accumulate 50 KES in wins', 'type' => 'wins', 'target' => 50, 'reward_type' => 'bonus_kes', 'reward_amount' => 10, 'is_daily' => true],
            ['name' => 'Hit Jackpot', 'description' => 'Hit any jackpot', 'type' => 'jackpots', 'target' => 1, 'reward_type' => 'free_spin', 'reward_free_spins' => 5, 'is_daily' => false],
        ];
        foreach ($missions as $m) LotteryMission::updateOrCreate(['name' => $m['name']], $m);
    }
}
