<?php
namespace App\Services;
use App\Models\LotteryMission;
use App\Models\LotteryUserMission;
use App\Models\User;
class LotteryMissionService
{
    public function track(User $user, string $type, int $increment = 1): void
    {
        $today = now()->toDateString();
        $missions = LotteryMission::where('type', $type)->get();
        foreach ($missions as $mission) {
            $userMission = LotteryUserMission::firstOrCreate([
                'user_id' => $user->id,
                'mission_id' => $mission->id,
                'date' => $today,
            ], ['progress' => 0, 'completed' => false]);
            if (!$userMission->completed) {
                $userMission->progress += $increment;
                if ($userMission->progress >= $mission->target) {
                    $userMission->completed = true;
                    $this->reward($user, $mission);
                }
                $userMission->save();
            }
        }
    }
    protected function reward(User $user, LotteryMission $mission): void
    {
        if ($mission->reward_type === 'free_spin') {
            $user->free_spins_available = ($user->free_spins_available ?? 0) + $mission->reward_free_spins;
            $user->save();
        } elseif ($mission->reward_type === 'bonus_kes') {
            $user->wallet->increment('balance', $mission->reward_amount);
            $user->transactions()->create([
                'type' => 'mission_reward',
                'amount' => $mission->reward_amount,
                'status' => 'completed',
                'description' => "Mission reward: {$mission->name}",
                'balance_after' => $user->wallet->balance,
                'user_id' => $user->id,
                'wallet_id' => $user->wallet->id,
            ]);
        }
    }
    public function getTodayMissions(User $user): array
    {
        $today = now()->toDateString();
        $allMissions = LotteryMission::all();
        $userMissions = LotteryUserMission::where('user_id', $user->id)
            ->where('date', $today)->get()->keyBy('mission_id');
        $result = [];
        foreach ($allMissions as $mission) {
            $um = $userMissions[$mission->id] ?? null;
            $result[] = [
                'id' => $mission->id,
                'name' => $mission->name,
                'description' => $mission->description,
                'progress' => $um ? $um->progress : 0,
                'target' => $mission->target,
                'completed' => $um ? $um->completed : false,
                'reward' => $mission->reward_type === 'free_spin' ? "{$mission->reward_free_spins} Free Spin(s)" : "KES {$mission->reward_amount}",
            ];
        }
        return $result;
    }
}
