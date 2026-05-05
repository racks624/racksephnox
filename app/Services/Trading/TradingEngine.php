<?php
namespace App\Services\Trading;

use App\Models\TradeOrder;
use App\Models\TradingPair;
use App\Models\TradingCandle;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TradingEngine
{
    protected $orderBook = [];
    protected $pair;

    public function __construct(TradingPair $pair = null)
    {
        if (!$pair) {
            $pair = TradingPair::firstOrCreate(
                ['symbol' => 'BTCUSDT'],
                ['base_currency' => 'BTC', 'quote_currency' => 'USDT', 'is_active' => true]
            );
        }
        $this->pair = $pair;
        $this->loadOrderBook();
    }

    protected function loadOrderBook()
    {
        $this->orderBook = Cache::remember("orderbook_{$this->pair->id}", 5, function () {
            $buyOrders = TradeOrder::where('pair_id', $this->pair->id)
                ->where('side', 'buy')->whereIn('status', ['pending', 'partial'])
                ->orderBy('limit_price', 'desc')->get();
            $sellOrders = TradeOrder::where('pair_id', $this->pair->id)
                ->where('side', 'sell')->whereIn('status', ['pending', 'partial'])
                ->orderBy('limit_price', 'asc')->get();
            return ['bids' => $buyOrders, 'asks' => $sellOrders];
        });
    }

    public function getMarketPrice()
    {
        return Cache::remember("market_price_{$this->pair->symbol}", 10, function () {
            $lastTrade = TradeOrder::where('pair_id', $this->pair->id)
                ->where('status', 'completed')->latest()->first();
            return $lastTrade ? $lastTrade->price_per_btc : 8500000;
        });
    }

    public function placeOrder(User $user, $side, $orderType, $amount, $price = null, $stopPrice = null, $tp = null, $sl = null, $tif = 'GTC')
    {
        $marketPrice = $this->getMarketPrice();
        $totalKes = ($orderType === 'market') ? $amount * $marketPrice : $amount * $price;

        if ($side === 'buy' && $user->tradingAccount->balance < $totalKes) {
            throw new \Exception('Insufficient trading balance');
        }
        if ($side === 'sell') {
            $btcBalance = $this->getBtcBalance($user->id);
            if ($btcBalance < $amount) throw new \Exception('Insufficient BTC balance');
        }

        $order = TradeOrder::create([
            'user_id' => $user->id,
            'pair_id' => $this->pair->id,
            'side' => $side,
            'order_type' => $orderType,
            'amount_btc' => $amount,
            'filled_amount' => 0,
            'limit_price' => $price,
            'stop_price' => $stopPrice,
            'take_profit_price' => $tp,
            'stop_loss_price' => $sl,
            'status' => 'pending',
            'time_in_force' => $tif,
            'expires_at' => ($tif !== 'GTC') ? now()->addMinutes(5) : null,
        ]);

        if ($orderType === 'market') {
            $this->executeMarketOrder($order);
        } else {
            $this->matchOrder($order);
        }
        return $order;
    }

    public function executeMarketOrder(TradeOrder $order)
    {
        $price = $this->getMarketPrice();
        $totalKes = $order->amount_btc * $price;
        DB::transaction(function () use ($order, $totalKes, $price) {
            if ($order->side === 'buy') {
                $order->user->tradingAccount->decrement('balance', $totalKes);
            } else {
                $order->user->tradingAccount->increment('balance', $totalKes);
            }
            $order->update([
                'filled_amount' => $order->amount_btc,
                'filled_kes' => $totalKes,
                'price_per_btc' => $price,
                'status' => 'completed'
            ]);
            $this->checkStopOrders($order);
            $this->trackAndAwardBonus($order->user);
            $this->executeCopyTrades($order);
        });
    }

    public function matchOrder(TradeOrder $order)
    {
        $opposite = $order->side === 'buy' ? 'sell' : 'buy';
        $matchingOrders = TradeOrder::where('pair_id', $this->pair->id)
            ->where('side', $opposite)
            ->where('order_type', 'limit')
            ->whereIn('status', ['pending', 'partial'])
            ->where('user_id', '!=', $order->user_id)
            ->when($order->side === 'buy', fn($q) => $q->where('limit_price', '<=', $order->limit_price))
            ->when($order->side === 'sell', fn($q) => $q->where('limit_price', '>=', $order->limit_price))
            ->orderBy('limit_price', $order->side === 'buy' ? 'asc' : 'desc')
            ->get();

        $remaining = $order->amount_btc - $order->filled_amount;
        foreach ($matchingOrders as $match) {
            if ($remaining <= 0) break;
            $fill = min($remaining, $match->amount_btc - $match->filled_amount);
            $fillPrice = $match->limit_price;
            $this->executeTrade($order, $match, $fill, $fillPrice);
            $remaining -= $fill;
        }
        $order->status = ($remaining <= 0) ? 'completed' : ($order->filled_amount > 0 ? 'partial' : 'pending');
        $order->save();
        $this->checkStopOrders($order);
    }

    protected function executeTrade(TradeOrder $buyOrder, TradeOrder $sellOrder, $amount, $price)
    {
        $totalKes = $amount * $price;
        DB::transaction(function () use ($buyOrder, $sellOrder, $amount, $totalKes, $price) {
            $buyOrder->increment('filled_amount', $amount);
            $buyOrder->increment('filled_kes', $totalKes);
            $buyOrder->price_per_btc = $price;
            if ($buyOrder->filled_amount >= $buyOrder->amount_btc) $buyOrder->status = 'completed';
            elseif ($buyOrder->filled_amount > 0) $buyOrder->status = 'partial';
            $buyOrder->save();

            $sellOrder->increment('filled_amount', $amount);
            $sellOrder->increment('filled_kes', $totalKes);
            $sellOrder->price_per_btc = $price;
            if ($sellOrder->filled_amount >= $sellOrder->amount_btc) $sellOrder->status = 'completed';
            elseif ($sellOrder->filled_amount > 0) $sellOrder->status = 'partial';
            $sellOrder->save();

            if ($buyOrder->user_id !== $sellOrder->user_id) {
                $buyOrder->user->tradingAccount->decrement('balance', $totalKes);
                $sellOrder->user->tradingAccount->increment('balance', $totalKes);
            }

            $this->checkStopOrders($buyOrder);
            $this->checkStopOrders($sellOrder);
            $this->trackAndAwardBonus($buyOrder->user);
            $this->trackAndAwardBonus($sellOrder->user);
            $this->executeCopyTrades($buyOrder);
            $this->executeCopyTrades($sellOrder);
        });
    }

    protected function checkStopOrders(TradeOrder $triggerOrder)
    {
        $price = $triggerOrder->price_per_btc;
        $stopOrders = TradeOrder::where('pair_id', $this->pair->id)
            ->where('status', 'pending')
            ->where('order_type', 'stop')
            ->where(function ($q) use ($price) {
                $q->where('stop_price', '<=', $price)->orWhere('stop_price', '>=', $price);
            })->get();
        foreach ($stopOrders as $order) {
            $order->order_type = 'market';
            $order->save();
            $this->executeMarketOrder($order);
        }
    }

    public function getOrderBook()
    {
        $bids = TradeOrder::where('pair_id', $this->pair->id)
            ->where('side', 'buy')
            ->whereIn('status', ['pending', 'partial'])
            ->get()
            ->groupBy('limit_price')
            ->map(fn($orders) => $orders->sum('amount_btc'))
            ->sortDesc()
            ->take(10);
        $asks = TradeOrder::where('pair_id', $this->pair->id)
            ->where('side', 'sell')
            ->whereIn('status', ['pending', 'partial'])
            ->get()
            ->groupBy('limit_price')
            ->map(fn($orders) => $orders->sum('amount_btc'))
            ->sort()
            ->take(10);
        return ['bids' => $bids, 'asks' => $asks];
    }

    public function getBtcBalance($userId)
    {
        $bought = TradeOrder::where('user_id', $userId)
            ->where('side', 'buy')
            ->where('status', 'completed')
            ->sum('filled_amount');
        $sold = TradeOrder::where('user_id', $userId)
            ->where('side', 'sell')
            ->where('status', 'completed')
            ->sum('filled_amount');
        return $bought - $sold;
    }

    public function trackAndAwardBonus($user)
    {
        $tracker = \App\Models\TradingBonusTracker::firstOrCreate(['user_id' => $user->id]);
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
                $user->tradingAccount->increment('balance', $bonusAmount);
                $user->transactions()->create([
                    'type' => 'trading_bonus',
                    'amount' => $bonusAmount,
                    'status' => 'completed',
                    'description' => 'Trading streak bonus (8 trades in 24h)',
                    'balance_after' => $user->tradingAccount->balance,
                    'user_id' => $user->id,
                    'wallet_id' => $user->tradingAccount->id,
                ]);
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

    public function executeCopyTrades(TradeOrder $originalOrder)
    {
        if ($originalOrder->status !== 'completed') return;
        $followers = \App\Models\FollowedTrader::where('trader_id', $originalOrder->user_id)
            ->where('auto_copy', true)
            ->with('follower')
            ->get();
        foreach ($followers as $follow) {
            $follower = $follow->follower;
            $copyRatio = $follow->copy_ratio / 100;
            $copiedAmount = $originalOrder->amount_btc * $copyRatio;
            if ($follow->max_copy_amount) {
                $maxBtc = $follow->max_copy_amount / $originalOrder->price_per_btc;
                if ($copiedAmount > $maxBtc) $copiedAmount = $maxBtc;
            }
            $totalKes = $copiedAmount * $originalOrder->price_per_btc;
            if ($follower->tradingAccount->balance < $totalKes) continue;
            try {
                DB::transaction(function () use ($follower, $originalOrder, $copiedAmount, $totalKes) {
                    TradeOrder::create([
                        'user_id' => $follower->id,
                        'pair_id' => $this->pair->id,
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
                    } else {
                        $follower->tradingAccount->increment('balance', $totalKes);
                    }
                    \App\Models\CopyTrade::create([
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
                Log::error("Copy trade failed for user {$follower->id}: " . $e->getMessage());
            }
        }
    }
}
