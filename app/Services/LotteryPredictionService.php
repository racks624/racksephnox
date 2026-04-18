<?php

namespace App\Services;

use App\Models\LotterySpin;
use App\Models\User;

class LotteryPredictionService
{
    public function analyze(User $user): array
    {
        $lastSpins = LotterySpin::where('user_id', $user->id)->latest()->take(20)->get();
        $totalSpins = $lastSpins->count();
        if ($totalSpins < 5) {
            return ['luck_score' => 50, 'suggestion' => 'Start with small bets to find your rhythm.'];
        }
        $wins = $lastSpins->where('win_amount', '>', 0)->count();
        $winRate = ($wins / $totalSpins) * 100;
        $recentWins = $lastSpins->take(5)->where('win_amount', '>', 0)->count();
        $luckScore = min(100, max(0, $winRate + ($recentWins * 5)));
        $suggestion = $luckScore > 70 ? 'Your luck is high! Consider increasing bet.' : ($luckScore < 30 ? 'Low luck period. Try free spin or lower bet.' : 'Steady luck. Play as usual.');
        return [
            'luck_score' => round($luckScore),
            'suggestion' => $suggestion,
            'win_rate' => round($winRate, 1),
            'recent_streak' => $recentWins,
        ];
    }
}
