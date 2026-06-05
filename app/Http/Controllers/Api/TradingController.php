<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Trading\TradingEngine;
use Illuminate\Http\Request;

class TradingController extends Controller
{
    protected $tradingEngine;

    public function __construct(TradingEngine $tradingEngine)
    {
        $this->tradingEngine = $tradingEngine;
    }

    public function balance(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'trading_balance' => $user->tradingAccount->balance ?? 0,
            'btc_balance' => $this->tradingEngine->getBtcBalance($user->id),
        ]);
    }

    public function price()
    {
        return response()->json(['price_kes' => $this->tradingEngine->getMarketPrice()]);
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
                $request->user(),
                'buy',
                $request->order_type,
                $request->amount_btc,
                $request->limit_price ?? null
            );
            return response()->json(['success' => true, 'order' => $order]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
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
                $request->user(),
                'sell',
                $request->order_type,
                $request->amount_btc,
                $request->limit_price ?? null
            );
            return response()->json(['success' => true, 'order' => $order]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function orders(Request $request)
    {
        $orders = $request->user()->tradeOrders()->latest()->take(50)->get();
        return response()->json($orders);
    }
}
