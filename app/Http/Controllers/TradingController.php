<?php
namespace App\Http\Controllers;
use App\Models\TradeOrder;
use App\Models\TradingAccount;
use App\Models\TradingPair;
use App\Models\CryptoPrice;
use App\Services\Trading\TradingEngine;
use App\Services\Trading\ChartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TradingController extends Controller
{
    protected $engine;
    protected $pair;
    public function __construct()
    {
        $this->pair = TradingPair::where('symbol', 'BTCUSDT')->firstOrFail();
        $this->engine = new TradingEngine($this->pair);
    }

    public function index()
    {
        $user = Auth::user();
        $tradingAccount = $user->tradingAccount ?? TradingAccount::create(['user_id' => $user->id]);
        $btcPrice = $this->engine->getMarketPrice();
        $openOrders = TradeOrder::where('user_id', $user->id)->whereIn('status', ['pending', 'partial'])->latest()->get();
        $orderHistory = TradeOrder::where('user_id', $user->id)->where('status', 'completed')->latest()->take(20)->get();
        $orderBook = $this->engine->getOrderBook();
        $chart = new ChartService();
        $candles = $chart->getCandles($this->pair->id, '1h', 100);
        $labels = $candles->pluck('open_time')->map(fn($d) => $d->format('H:i'));
        $prices = $candles->pluck('close');

        return view('trading.index', compact(
            'tradingAccount', 'btcPrice', 'openOrders', 'orderHistory',
            'orderBook', 'candles', 'labels', 'prices'
        ));
    }

    public function buy(Request $request)
    {
        $request->validate([
            'amount_btc' => 'required|numeric|min:' . $this->pair->min_trade_amount,
            'order_type' => 'required|in:market,limit,stop',
            'price' => 'required_if:order_type,limit,stop|numeric|min:0',
            'take_profit' => 'nullable|numeric|min:0',
            'stop_loss' => 'nullable|numeric|min:0',
            'time_in_force' => 'in:GTC,IOC,FOK'
        ]);
        try {
            $order = $this->engine->placeOrder(
                Auth::user(), 'buy', $request->order_type, $request->amount_btc,
                $request->price ?? null, $request->stop_price ?? null,
                $request->take_profit, $request->stop_loss, $request->time_in_force ?? 'GTC'
            );
            return back()->with('success', "Buy order placed. Order #{$order->id}");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function sell(Request $request)
    {
        $request->validate([
            'amount_btc' => 'required|numeric|min:' . $this->pair->min_trade_amount,
            'order_type' => 'required|in:market,limit,stop',
            'price' => 'required_if:order_type,limit,stop|numeric|min:0',
            'take_profit' => 'nullable|numeric|min:0',
            'stop_loss' => 'nullable|numeric|min:0'
        ]);
        try {
            $order = $this->engine->placeOrder(
                Auth::user(), 'sell', $request->order_type, $request->amount_btc,
                $request->price ?? null, $request->stop_price ?? null,
                $request->take_profit, $request->stop_loss
            );
            return back()->with('success', "Sell order placed. Order #{$order->id}");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function cancelOrder(TradeOrder $order)
    {
        if ($order->user_id !== Auth::id() || !in_array($order->status, ['pending', 'partial'])) {
            return back()->withErrors('Cannot cancel');
        }
        $order->status = 'cancelled';
        $order->save();
        return back()->with('success', 'Order cancelled');
    }

    public function orderBook()
    {
        return response()->json($this->engine->getOrderBook());
    }

    public function candles(Request $request, $interval = '1h')
    {
        $chart = new ChartService();
        $candles = $chart->getCandles($this->pair->id, $interval, 100);
        return response()->json($candles);
    }
}
