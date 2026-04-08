<?php

namespace App\Services\Trading;

use App\Models\TradeOrder;
use App\Models\User;
use App\Models\CryptoPrice;
use App\Models\TradingBonusTracker;
use App\Models\FollowedTrader;
use App\Models\CopyTrade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TradingEngine
{
    /**
     * Get current BTC/KES market price (cached).
     */
    public function getMarketPrice()
    {
        return Cache::remember('btc_market_price', 10, function () {
            $price = CryptoPrice::where('symbol', 'BTC')->first();
            return $price ? $price->price_kes : 0;
        });
    }

    /**
     * Place a new order (buy/sell).
     */
    public function placeOrder(User $user, $side, $orderType, $amountBtc, $limitPrice = null, $stopPrice = null)
    {
        $marketPrice = $this->getMarketPrice();
        
        if ($side === 'buy') {
            $totalKes = $orderType === 'market' ? $amountBtc * $marketPrice : $amountBtc * $limitPrice;
            if ($user->tradingAccount->balance < $totalKes) {
                throw new \Exception('Insufficient trading balance');
            }
        } else {
            $btcBalance = $this->getBtcBalance($user->id);
            if ($btcBalance < $amountBtc) {
                throw new \Exception('Insufficient BTC balance');
            }
        }

        $order = TradeOrder::create([
            'user_id' => $user->id,
            'side' => $side,
            'order_type' => $orderType,
            'amount_btc' => $amountBtc,
            'filled_amount' => 0,
            'limit_price' => $limitPrice,
            'stop_price' => $stopPrice,
            'status' => 'pending',
        ]);

        if ($orderType === 'market') {
            $this->executeMarketOrder($order);
        } else {
            $this->matchOrder($order);
        }

        return $order;
    }

    /**
     * Execute a market order immediately.
     */
    public function executeMarketOrder(TradeOrder $order)
    {
        $price = $this->getMarketPrice();
        $totalKes = $order->amount_btc * $price;

        DB::transaction(function () use ($order, $totalKes, $price) {
            if ($order->side === 'buy') {
                $order->user->tradingAccount->decrement('balance', $totalKes);
                $order->user->tradingAccount->debit($totalKes, "Market buy {$order->amount_btc} BTC at KES {$price}");
            } else {
                $order->user->tradingAccount->increment('balance', $totalKes);
                $order->user->tradingAccount->credit($totalKes, "Market sell {$order->amount_btc} BTC at KES {$price}");
            }

            $order->update([
                'filled_amount' => $order->amount_btc,
                'filled_kes' => $totalKes,
                'price_per_btc' => $price,
                'status' => 'completed',
            ]);

            $this->trackAndAwardBonus($order->user);
            $this->executeCopyTrades($order);
        });
    }

    /**
     * Simple matching engine for limit orders.
     */
    public function matchOrder(TradeOrder $order)
    {
        $oppositeSide = $order->side === 'buy' ? 'sell' : 'buy';
        
        $matchingOrders = TradeOrder::where('user_id', '!=', $order->user_id)
            ->where('side', $oppositeSide)
            ->where('order_type', 'limit')
            ->where('status', 'pending')
            ->where(function ($q) use ($order) {
                if ($order->side === 'buy') {
                    $q->where('limit_price', '<=', $order->limit_price);
                } else {
                    $q->where('limit_price', '>=', $order->limit_price);
                }
            })
            ->orderBy('limit_price', $order->side === 'buy' ? 'asc' : 'desc')
            ->get();

        $remainingAmount = $order->getRemainingAmount();
        
        foreach ($matchingOrders as $match) {
            if ($remainingAmount <= 0) break;
            
            $matchRemaining = $match->getRemainingAmount();
            $fillAmount = min($remainingAmount, $matchRemaining);
            $fillPrice = $match->limit_price;
            
            $this->executeTrade($order, $match, $fillAmount, $fillPrice);
            $remainingAmount -= $fillAmount;
        }

        if ($remainingAmount <= 0) {
            $order->status = 'completed';
        } else {
            $order->status = 'partial';
        }
        $order->save();
    }

    /**
     * Execute a trade between two orders.
     */
    protected function executeTrade(TradeOrder $buyOrder, TradeOrder $sellOrder, $amount, $price)
    {
        $totalKes = $amount * $price;
        
        DB::transaction(function () use ($buyOrder, $sellOrder, $amount, $totalKes, $price) {
            $buyOrder->increment('filled_amount', $amount);
            $buyOrder->increment('filled_kes', $totalKes);
            if ($buyOrder->filled_amount >= $buyOrder->amount_btc) {
                $buyOrder->status = 'completed';
                $buyOrder->price_per_btc = $price;
            } else {
                $buyOrder->status = 'partial';
            }
            $buyOrder->save();

            $sellOrder->increment('filled_amount', $amount);
            $sellOrder->increment('filled_kes', $totalKes);
            if ($sellOrder->filled_amount >= $sellOrder->amount_btc) {
                $sellOrder->status = 'completed';
                $sellOrder->price_per_btc = $price;
            } else {
                $sellOrder->status = 'partial';
            }
            $sellOrder->save();

            if ($buyOrder->user_id !== $sellOrder->user_id) {
                $buyOrder->user->tradingAccount->decrement('balance', $totalKes);
                $sellOrder->user->tradingAccount->increment('balance', $totalKes);
            }

            $this->trackAndAwardBonus($buyOrder->user);
            $this->trackAndAwardBonus($sellOrder->user);
            $this->executeCopyTrades($buyOrder);
            $this->executeCopyTrades($sellOrder);
        });
    }

    /**
     * Get user's BTC balance (sum of buys minus sells).
     */
    public function getBtcBalance($userId)
    {
        $bought = TradeOrder::where('user_id', $userId)->where('side', 'buy')->where('status', 'completed')->sum('filled_amount');
        $sold = TradeOrder::where('user_id', $userId)->where('side', 'sell')->where('status', 'completed')->sum('filled_amount');
        return $bought - $sold;
    }

    /**
     * Track and award trading bonus (8% of trading balance after 8 trades in 24h)
     */
    public function trackAndAwardBonus($user)
    {
        $tracker = TradingBonusTracker::firstOrCreate(['user_id' => $user->id]);

        $lastTrade = TradeOrder::where('user_id', $user->id)
            ->where('status', 'completed')
            ->latest()
            ->first();

        if ($lastTrade && $lastTrade->created_at->lt(now()->subHours(24))) {
            $tracker->trade_count_24h = 0;
        }

        $tracker->increment('trade_count_24h');
        
        if ($tracker->trade_count_24h >= 8 &&
            (!$tracker->last_bonus_awarded_at || $tracker->last_bonus_awarded_at->lt(now()->subHours(24)))) {
            
            $bonusAmount = $user->tradingAccount->balance * 0.08;
            
            DB::transaction(function () use ($user, $bonusAmount, $tracker) {
                $user->tradingAccount->credit($bonusAmount, 'Trading streak bonus (8 trades in 24h)');
                $user->notify(new \App\Notifications\BonusNotification($bonusAmount, 'trading_bonus'));
                $tracker->last_bonus_awarded_at = now();
                $tracker->save();
            });
            
            $tracker->trade_count_24h = 0;
            $tracker->save();
            
            return $bonusAmount;
        }
        
        $tracker->save();
        return null;
    }

    /**
     * Execute copy trades for followers when a trade is completed
     */
    public function executeCopyTrades(TradeOrder $originalOrder)
    {
        if ($originalOrder->status !== 'completed') {
            return;
        }

        $followers = FollowedTrader::where('trader_id', $originalOrder->user_id)
            ->where('auto_copy', true)
            ->with('follower')
            ->get();

        foreach ($followers as $follow) {
            $follower = $follow->follower;
            $copyRatio = $follow->copy_ratio / 100;
            $copiedAmount = $originalOrder->amount_btc * $copyRatio;

            if ($follow->max_copy_amount) {
                $maxBtc = $follow->max_copy_amount / $originalOrder->price_per_btc;
                if ($copiedAmount > $maxBtc) {
                    $copiedAmount = $maxBtc;
                }
            }

            $totalKes = $copiedAmount * $originalOrder->price_per_btc;
            
            if ($follower->tradingAccount->balance < $totalKes) {
                continue;
            }

            try {
                DB::transaction(function () use ($follower, $originalOrder, $copiedAmount, $totalKes) {
                    $copyOrder = TradeOrder::create([
                        'user_id' => $follower->id,
                        'side' => $originalOrder->side,
                        'order_type' => 'market',
                        'amount_btc' => $copiedAmount,
                        'filled_amount' => $copiedAmount,
                        'filled_kes' => $totalKes,
                        'price_per_btc' => $originalOrder->price_per_btc,
                        'status' => 'completed',
                    ]);

                    if ($originalOrder->side === 'buy') {
                        $follower->tradingAccount->decrement('balance', $totalKes);
                        $follower->tradingAccount->debit($totalKes, "Copy trade: bought {$copiedAmount} BTC at KES {$originalOrder->price_per_btc}");
                    } else {
                        $follower->tradingAccount->increment('balance', $totalKes);
                        $follower->tradingAccount->credit($totalKes, "Copy trade: sold {$copiedAmount} BTC at KES {$originalOrder->price_per_btc}");
                    }

                    CopyTrade::create([
                        'original_order_id' => $originalOrder->id,
                        'follower_id' => $follower->id,
                        'trader_id' => $originalOrder->user_id,
                        'original_amount' => $originalOrder->amount_btc,
                        'copied_amount' => $copiedAmount,
                        'original_price' => $originalOrder->price_per_btc,
                        'copied_kes' => $totalKes,
                        'side' => $originalOrder->side,
                        'status' => 'executed',
                    ]);
                });
            } catch (\Exception $e) {
                \Log::error("Copy trade failed for user {$follower->id}: " . $e->getMessage());
            }
        }
    }
}
