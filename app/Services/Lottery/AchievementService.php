<?php
namespace App\Services\Lottery;
use App\Models\LotteryAchievement;
use App\Models\LotteryUserAchievement;
use App\Models\User;
class AchievementService
{
    public function checkAndAward(User $user, string $type, int $value = 1): void
    {
        $achievements = LotteryAchievement::where('condition_type', $type)
            ->where('condition_value', '<=', $value)
            ->get();
        foreach ($achievements as $ach) {
            $exists = LotteryUserAchievement::where('user_id', $user->id)
                ->where('achievement_id', $ach->id)->exists();
            if (!$exists) {
                LotteryUserAchievement::create([
                    'user_id' => $user->id,
                    'achievement_id' => $ach->id,
                    'achieved_at' => now(),
                ]);
                if ($ach->reward_free_spins > 0) {
                    $user->free_spins_available = ($user->free_spins_available ?? 0) + $ach->reward_free_spins;
                    $user->save();
                }
            }
        }
    }
}
