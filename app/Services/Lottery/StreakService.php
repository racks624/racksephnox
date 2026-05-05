<?php
namespace App\Services\Lottery;
use App\Models\LotteryDailyStreak;
use App\Models\User;
class StreakService
{
    public function checkAndReward(User $user): void
    {
        $today = now()->toDateString();
        $streak = LotteryDailyStreak::firstOrCreate(
            ['user_id' => $user->id],
            ['streak_count' => 0, 'last_login_date' => $today, 'reward_claimed' => false]
        );
        $yesterday = now()->subDay()->toDateString();
        if ($streak->last_login_date == $yesterday) {
            $streak->streak_count++;
        } elseif ($streak->last_login_date != $today) {
            $streak->streak_count = 1;
        }
        if ($streak->last_login_date != $today && !$streak->reward_claimed) {
            $this->giveReward($user, $streak->streak_count);
            $streak->reward_claimed = true;
        }
        $streak->last_login_date = $today;
        $streak->save();
    }
    protected function giveReward(User $user, int $streak): void
    {
        $rewards = [7 => 5, 14 => 10, 30 => 25];
        if (isset($rewards[$streak])) {
            $user->free_spins_available = ($user->free_spins_available ?? 0) + $rewards[$streak];
            $user->save();
        }
    }
}
