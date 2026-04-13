<?php

namespace Database\Seeders;

use App\Models\LotteryAchievement;
use Illuminate\Database\Seeder;

class LotteryAchievementSeeder extends Seeder
{
    public function run()
    {
        $achievements = [
            ['name' => 'First Spin', 'description' => 'Play your first lottery game', 'condition_type' => 'total_spins', 'condition_value' => 1, 'reward_free_spins' => 1],
            ['name' => 'Lucky Beginner', 'description' => 'Win 5 times', 'condition_type' => 'total_wins', 'condition_value' => 5, 'reward_free_spins' => 2],
            ['name' => 'Jackpot Hunter', 'description' => 'Hit the progressive jackpot', 'condition_type' => 'jackpot_hit', 'condition_value' => 1, 'reward_free_spins' => 10],
            ['name' => 'High Roller', 'description' => 'Bet over 10,000 KES total', 'condition_type' => 'total_bet', 'condition_value' => 10000, 'reward_free_spins' => 5],
            ['name' => 'Streak Master', 'description' => 'Win 3 times in a row', 'condition_type' => 'win_streak', 'condition_value' => 3, 'reward_free_spins' => 3],
        ];
        foreach ($achievements as $ach) {
            LotteryAchievement::updateOrCreate(['name' => $ach['name']], $ach);
        }
    }
}
