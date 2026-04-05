<?php

namespace App\Http\Controllers;

use App\Models\CryptoPrice;
use App\Models\TradeOrder;
use App\Models\TradingAccount;
use App\Models\BtcPriceHistory;
use App\Services\Trading\TradingEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TradingController extends Controller
{
    protected $tradingEngine;

    public function __construct(TradingEngine $tradingEngine)
    {
        $this->tradingEngine = $tradingEngine;
    }

    public function index()
    {
        $user = Auth::user();
        $tradingAccount = $user->tradingAccount ?? $user->tradingAccount()->create(['balance' => 0]);
        $wallet = $user->wallet;

        $btcPrice = $this->tradingEngine->getMarketPrice();
        $btcBalance = $this->tradingEngine->getBtcBalance($user->id);

        // Open orders (pending/partial)
        $openOrders = TradeOrder::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'partial'])
            ->latest()
            ->get();

        // Completed orders (last 20)
        $completedOrders = TradeOrder::where('user_id', $user->id)
            ->where('status', 'completed')
            ->latest()
            ->take(20)
            ->get();

        // Price history for chart (last 30 days)
        $priceHistory = BtcPriceHistory::where('recorded_at', '>=', now()->subDays(30))
            ->orderBy('recorded_at')
            ->get();

        // Stats
        $totalBuyVolume = TradeOrder::where('user_id', $user->id)->where('side', 'buy')->where('status', 'completed')->sum('filled_amount');
        $totalSellVolume = TradeOrder::where('user_id', $user->id)->where('side', 'sell')->where('status', 'completed')->sum('filled_amount');
        $totalInvested = TradeOrder::where('user_id', $user->id)->where('side', 'buy')->where('status', 'completed')->sum('filled_kes');
        $totalRealized = TradeOrder::where('user_id', $user->id)->where('side', 'sell')->where('status', 'completed')->sum('filled_kes');
        $pnl = $totalRealized - $totalInvested;

        return view('trading', compact(
            'tradingAccount', 'wallet', 'btcPrice', 'btcBalance', 'openOrders', 'completedOrders', 'priceHistory',
            'totalBuyVolume', 'totalSellVolume', 'totalInvested', 'totalRealized', 'pnl'
        ));
    }

    public function buy(Request $request)
    {
        $request->validate([
            'amount_btc' => 'required|numeric|min:0.0001',
            'order_type' => 'required|in:market,limit',
            'limit_price' => 'required_if:order_type,limit|numeric|min:0',
        ]);

        try {
            $order = $this->tradingEngine->placeOrder(
                Auth::user(),
                'buy',
                $request->order_type,
                $request->amount_btc,
                $request->order_type === 'limit' ? $request->limit_price : null
            );
            return redirect()->route('trading')->with('success', 'Order placed successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function sell(Request $request)
    {
        $request->validate([
            'amount_btc' => 'required|numeric|min:0.0001',
            'order_type' => 'required|in:market,limit',
            'limit_price' => 'required_if:order_type,limit|numeric|min:0',
        ]);

        try {
            $order = $this->tradingEngine->placeOrder(
                Auth::user(),
                'sell',
                $request->order_type,
                $request->amount_btc,
                $request->order_type === 'limit' ? $request->limit_price : null
            );
            return redirect()->route('trading')->with('success', 'Order placed successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function cancelOrder(TradeOrder $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        if (!in_array($order->status, ['pending', 'partial'])) {
            return back()->withErrors(['error' => 'Order cannot be cancelled']);
        }
        $order->update(['status' => 'cancelled']);
        return back()->with('success', 'Order cancelled.');
    }

    public function transfer(Request $request)
    {
        // Transfer between wallet and trading account (same as before)
        $request->validate([
            'amount' => 'required|numeric|min:10',
            'direction' => 'required|in:to_trading,to_wallet',
        ]);
        // ... existing transfer logic (unchanged)
    }

    public function apiBalance()
    {
        $user = Auth::user();
        return response()->json([
            'wallet_balance' => $user->wallet->balance,
            'trading_balance' => $user->tradingAccount->balance ?? 0,
            'btc_balance' => $this->tradingEngine->getBtcBalance($user->id),
        ]);
    }

    public function apiPrice()
    {
        return response()->json(['price_kes' => $this->tradingEngine->getMarketPrice()]);
    }

    public function apiOrders()
    {
        $user = Auth::user();
        $orders = TradeOrder::where('user_id', $user->id)->latest()->take(50)->get();
        return response()->json($orders);
    }
}
