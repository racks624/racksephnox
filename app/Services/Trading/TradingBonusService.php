<?php

namespace App\Services\Trading;

use App\Models\TradeOrder;
use App\Models\User;
use App\Notifications\BonusNotification;
use Illuminate\Support\Facades\DB;

class TradingBonusService
{
    /**
     * Check if user qualifies for trading streak bonus
     * and award if eligible.
     */
    public function checkAndAwardBonus(User $user)
    {
        // Count completed trades in last 24 hours
        $tradesCount = TradeOrder::where('user_id', $user->id)
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        if ($tradesCount >= 8) {
            // Check if bonus already awarded in this 24h window
            $bonusAlreadyAwarded = TradeOrder::where('user_id', $user->id)
                ->where('order_type', 'bonus')
                ->where('created_at', '>=', now()->subHours(24))
                ->exists();

            if (!$bonusAlreadyAwarded) {
                $tradingBalance = $user->tradingAccount->balance ?? 0;
                $bonusAmount = $tradingBalance * 0.08; // 8%

                if ($bonusAmount > 0) {
                    DB::transaction(function () use ($user, $bonusAmount) {
                        // Credit the bonus to trading account
                        $user->tradingAccount->increment('balance', $bonusAmount);
                        $user->tradingAccount->credit($bonusAmount, 'Trading streak bonus (8 trades in 24h)');

                        // Record a dummy "bonus" order to prevent duplicate awards
                        TradeOrder::create([
                            'user_id' => $user->id,
                            'side' => 'bonus',
                            'order_type' => 'bonus',
                            'amount_btc' => 0,
                            'filled_amount' => 0,
                            'status' => 'completed',
                        ]);

                        // Send notification
                        $user->notify(new BonusNotification($bonusAmount, 'trading_bonus'));
                    });
                }
            }
        }
    }
}
