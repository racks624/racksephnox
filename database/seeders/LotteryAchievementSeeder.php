<?php
namespace Database\Seeders;
use App\Models\LotteryAchievement;
use Illuminate\Database\Seeder;
class LotteryAchievementSeeder extends Seeder
{
    public function run()
    {
        $achievements = [
            ['name' => 'First Spin', 'description' => 'Play your first lottery spin', 'condition_type' => 'spins', 'condition_value' => 1, 'reward_free_spins' => 1],
            ['name' => '10 Spins', 'description' => 'Complete 10 spins', 'condition_type' => 'spins', 'condition_value' => 10, 'reward_free_spins' => 2],
            ['name' => '100 Spins', 'description' => 'Complete 100 spins', 'condition_type' => 'spins', 'condition_value' => 100, 'reward_free_spins' => 5],
            ['name' => 'First Win', 'description' => 'Win your first prize', 'condition_type' => 'wins', 'condition_value' => 1, 'reward_free_spins' => 1],
            ['name' => 'Jackpot Hunter', 'description' => 'Hit any jackpot', 'condition_type' => 'jackpots', 'condition_value' => 1, 'reward_free_spins' => 10],
        ];
        foreach ($achievements as $ach) LotteryAchievement::updateOrCreate(['name' => $ach['name']], $ach);
    }
}
