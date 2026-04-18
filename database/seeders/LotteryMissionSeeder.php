<?php

namespace Database\Seeders;

use App\Models\LotteryMission;
use Illuminate\Database\Seeder;

class LotteryMissionSeeder extends Seeder
{
    public function run()
    {
        $missions = [
            ['name' => 'Spin Master', 'description' => 'Spin the reels 10 times', 'type' => 'spins', 'target' => 10, 'reward_type' => 'free_spin', 'reward_free_spins' => 1],
            ['name' => 'Lucky Day', 'description' => 'Win 3 times', 'type' => 'wins', 'target' => 3, 'reward_type' => 'bonus_kes', 'reward_amount' => 50],
            ['name' => 'Jackpot Seeker', 'description' => 'Hit any jackpot (mini or super)', 'type' => 'jackpot', 'target' => 1, 'reward_type' => 'free_spin', 'reward_free_spins' => 3],
            ['name' => 'Social Butterfly', 'description' => 'Refer a friend', 'type' => 'referral', 'target' => 1, 'reward_type' => 'bonus_kes', 'reward_amount' => 100],
        ];
        foreach ($missions as $mission) {
            LotteryMission::updateOrCreate(['name' => $mission['name']], $mission);
        }
    }
}
