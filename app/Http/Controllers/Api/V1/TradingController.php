<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Trading\TradingEngine;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TradingController extends Controller
{
    use ApiResponse;

    protected $tradingEngine;

    public function __construct(TradingEngine $tradingEngine)
    {
        $this->tradingEngine = $tradingEngine;
    }

    public function balance(Request $request)
    {
        $user = $request->user();
        return $this->successResponse([
            'trading_balance' => $user->tradingAccount->balance ?? 0,
            'btc_balance' => $this->tradingEngine->getBtcBalance($user->id),
        ]);
    }

    public function price()
    {
        return $this->successResponse(['price_kes' => $this->tradingEngine->getMarketPrice()]);
    }

    public function buy(Request $request)
    {
        $request->validate([
            'amount_btc' => 'required|numeric|min:0.0001',
            'order_type' => 'required|in:market,limit',
            'limit_price' => 'required_if:order_type,limit|numeric|min:0',
        ]);

        $price = $this->tradingEngine->getMarketPrice();
        $minBtc = 350 / $price;
        if ($request->amount_btc < $minBtc) {
            return $this->errorResponse('Minimum trade amount is KES 350.', 422);
        }

        try {
            $order = $this->tradingEngine->placeOrder(
                $request->user(),
                'buy',
                $request->order_type,
                $request->amount_btc,
                $request->limit_price ?? null
            );
            return $this->successResponse($order, 'Buy order placed successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function sell(Request $request)
    {
        $request->validate([
            'amount_btc' => 'required|numeric|min:0.0001',
            'order_type' => 'required|in:market,limit',
            'limit_price' => 'required_if:order_type,limit|numeric|min:0',
        ]);

        $price = $this->tradingEngine->getMarketPrice();
        $minBtc = 350 / $price;
        if ($request->amount_btc < $minBtc) {
            return $this->errorResponse('Minimum trade amount is KES 350.', 422);
        }

        try {
            $order = $this->tradingEngine->placeOrder(
                $request->user(),
                'sell',
                $request->order_type,
                $request->amount_btc,
                $request->limit_price ?? null
            );
            return $this->successResponse($order, 'Sell order placed successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function orders(Request $request)
    {
        $orders = $request->user()->tradeOrders()->latest()->take(50)->get();
        return $this->successResponse($orders);
    }
}
