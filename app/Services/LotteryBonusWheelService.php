<?php

namespace App\Services;

use App\Models\LotteryBonusWheel;
use App\Models\LotteryBonusWheelSpin;
use App\Models\User;

class LotteryBonusWheelService
{
    public function canSpin(User $user): bool
    {
        $lastSpin = LotteryBonusWheelSpin::where('user_id', $user->id)->whereDate('created_at', today())->exists();
        return !$lastSpin;
    }

    public function spin(User $user): array
    {
        if (!$this->canSpin($user)) throw new \Exception('You have already spun the wheel today.');
        $wheel = LotteryBonusWheel::where('is_active', true)->first();
        if (!$wheel) throw new \Exception('Bonus wheel not available.');
        $segments = $wheel->segments;
        $rand = random_int(0, count($segments) - 1);
        $prize = $segments[$rand];

        $wallet = $user->wallet;
        if ($prize['type'] === 'kes') {
            $wallet->increment('balance', $prize['value']);
            $user->transactions()->create([
                'type' => 'bonus_wheel',
                'amount' => $prize['value'],
                'status' => 'completed',
                'description' => "Bonus Wheel: KES {$prize['value']}",
                'balance_after' => $wallet->balance,
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
            ]);
        } elseif ($prize['type'] === 'free_spin') {
            $user->free_spins_available = ($user->free_spins_available ?? 0) + $prize['value'];
            $user->save();
            $user->transactions()->create([
                'type' => 'bonus_wheel',
                'amount' => 0,
                'status' => 'completed',
                'description' => "Bonus Wheel: {$prize['value']} Free Spin(s)",
                'balance_after' => $wallet->balance,
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
            ]);
        }

        LotteryBonusWheelSpin::create([
            'user_id' => $user->id,
            'wheel_id' => $wheel->id,
            'prize_type' => $prize['type'],
            'prize_value' => $prize['value'],
            'spun_at' => now(),
        ]);

        return $prize;
    }
}
