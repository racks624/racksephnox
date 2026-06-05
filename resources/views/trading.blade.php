@extends('layouts.app')

@section('content')
<div x-data="tradingManager()" x-init="init()" class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold golden-title">BTC/KES Trading Console</h1>
            <p class="text-gold-400 mt-2">Advanced orders • Real‑time market • Limit & Market</p>
        </div>

        <!-- Balance Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="card-golden p-5">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gold-400 text-sm">Wallet Balance</p>
                        <p class="text-2xl font-bold text-gold">KES {{ number_format($wallet->balance, 2) }}</p>
                    </div>
                    <i class="fas fa-wallet text-3xl text-gold/50"></i>
                </div>
                <form action="{{ route('trading.transfer') }}" method="POST" class="mt-4 flex gap-2">
                    @csrf
                    <input type="hidden" name="direction" value="to_trading">
                    <input type="number" name="amount" placeholder="Amount" class="input-golden flex-1 text-sm" required>
                    <button type="submit" class="btn-golden text-sm py-2 px-3">→ Trading</button>
                </form>
            </div>
            <div class="card-golden p-5">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gold-400 text-sm">Trading Balance</p>
                        <p class="text-2xl font-bold text-gold">KES {{ number_format($tradingAccount->balance, 2) }}</p>
                    </div>
                    <i class="fas fa-chart-line text-3xl text-gold/50"></i>
                </div>
                <form action="{{ route('trading.transfer') }}" method="POST" class="mt-4 flex gap-2">
                    @csrf
                    <input type="hidden" name="direction" value="to_wallet">
                    <input type="number" name="amount" placeholder="Amount" class="input-golden flex-1 text-sm" required>
                    <button type="submit" class="btn-golden text-sm py-2 px-3">→ Wallet</button>
                </form>
            </div>
            <div class="card-golden p-5">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gold-400 text-sm">BTC Balance</p>
                        <p class="text-2xl font-bold text-gold">{{ number_format($btcBalance, 6) }} BTC</p>
                    </div>
                    <i class="fab fa-bitcoin text-3xl text-gold/50"></i>
                </div>
                <p class="mt-2 text-xs text-ivory/50">P&L: <span class="{{ $pnl >= 0 ? 'text-green-400' : 'text-red-400' }}">KES {{ number_format($pnl, 2) }}</span></p>
            </div>
        </div>

        <!-- Price and Chart -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
            <div class="lg:col-span-2 card-golden p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gold">BTC/KES Chart</h3>
                    <div class="flex gap-2">
                        <button @click="setInterval('1D')" class="text-xs px-2 py-1 bg-gold/10 rounded">1D</button>
                        <button @click="setInterval('1W')" class="text-xs px-2 py-1 bg-gold/10 rounded">1W</button>
                        <button @click="setInterval('1M')" class="text-xs px-2 py-1 bg-gold/10 rounded">1M</button>
                    </div>
                </div>
                <!-- TradingView Widget -->
                <div id="tradingview-widget" style="height: 400px;"></div>
                <div class="mt-2 text-center">
                    <span class="text-2xl font-bold text-gold" x-text="'KES ' + currentPrice.toLocaleString()"></span>
                    <span class="text-sm text-ivory/50 ml-2">live</span>
                </div>
            </div>

            <!-- Order Form -->
            <div class="card-golden p-5">
                <div class="flex border-b border-gold/30 mb-4">
                    <button @click="activeOrder = 'buy'" :class="{'border-gold text-gold': activeOrder === 'buy'}" class="flex-1 py-2 text-center border-b-2 border-transparent">Buy BTC</button>
                    <button @click="activeOrder = 'sell'" :class="{'border-gold text-gold': activeOrder === 'sell'}" class="flex-1 py-2 text-center border-b-2 border-transparent">Sell BTC</button>
                </div>

                <form x-show="activeOrder === 'buy'" @submit.prevent="submitOrder('buy')">
                    <div class="mb-4">
                        <label class="block text-sm text-gold-400">Amount (BTC)</label>
                        <input type="number" step="0.0001" x-model="buyAmount" class="input-golden w-full" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm text-gold-400">Order Type</label>
                        <select x-model="buyOrderType" class="input-golden w-full">
                            <option value="market">Market</option>
                            <option value="limit">Limit</option>
                        </select>
                    </div>
                    <div x-show="buyOrderType === 'limit'" class="mb-4">
                        <label class="block text-sm text-gold-400">Limit Price (KES)</label>
                        <input type="number" step="0.01" x-model="buyLimitPrice" class="input-golden w-full">
                    </div>
                    <div class="mb-4 p-3 bg-gold/10 rounded-lg">
                        <div class="flex justify-between">
                            <span class="text-sm">Total Cost:</span>
                            <span class="text-gold font-bold" x-text="'KES ' + formatNumber(buyTotal)"></span>
                        </div>
                    </div>
                    <button type="submit" :disabled="isSubmitting" class="btn-golden w-full">Place Buy Order</button>
                </form>

                <form x-show="activeOrder === 'sell'" @submit.prevent="submitOrder('sell')">
                    <div class="mb-4">
                        <label class="block text-sm text-gold-400">Amount (BTC)</label>
                        <input type="number" step="0.0001" x-model="sellAmount" class="input-golden w-full" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm text-gold-400">Order Type</label>
                        <select x-model="sellOrderType" class="input-golden w-full">
                            <option value="market">Market</option>
                            <option value="limit">Limit</option>
                        </select>
                    </div>
                    <div x-show="sellOrderType === 'limit'" class="mb-4">
                        <label class="block text-sm text-gold-400">Limit Price (KES)</label>
                        <input type="number" step="0.01" x-model="sellLimitPrice" class="input-golden w-full">
                    </div>
                    <div class="mb-4 p-3 bg-gold/10 rounded-lg">
                        <div class="flex justify-between">
                            <span class="text-sm">Total Value:</span>
                            <span class="text-gold font-bold" x-text="'KES ' + formatNumber(sellTotal)"></span>
                        </div>
                    </div>
                    <button type="submit" :disabled="isSubmitting" class="btn-golden w-full">Place Sell Order</button>
                </form>
                <div x-show="orderError" x-text="orderError" class="mt-4 text-red-400 text-sm"></div>
            </div>
        </div>

        <!-- Open Orders -->
        <div class="card-golden p-6 mb-10">
            <h2 class="text-xl font-bold text-gold mb-4">Open Orders</h2>
            @if($openOrders->count())
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gold/30">
                            指数
                                <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Side</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Amount (BTC)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Limit Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Filled</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Actions</th>
                             </thead>
                        <tbody class="divide-y divide-gold/20">
                            @foreach($openOrders as $order)
                            <tr>
                                <td class="px-4 py-3">{{ strtoupper($order->side) }}</td>
                                <td class="px-4 py-3">{{ ucfirst($order->order_type) }}</td>
                                <td class="px-4 py-3">{{ number_format($order->amount_btc, 6) }}</td>
                                <td class="px-4 py-3">{{ $order->limit_price ? 'KES '.number_format($order->limit_price, 2) : '-' }}</td>
                                <td class="px-4 py-3">{{ number_format($order->filled_amount, 6) }} / {{ number_format($order->amount_btc, 6) }}</td>
                                <td class="px-4 py-3">{{ ucfirst($order->status) }}</td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('trading.cancel', $order) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-red-400 hover:text-red-300">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                     </table>
                </div>
            @else
                <p class="text-center text-ivory/50 py-4">No open orders.</p>
            @endif
        </div>

        <!-- Order History -->
        <div class="card-golden p-6">
            <h2 class="text-xl font-bold text-gold mb-4">Order History</h2>
            @if($completedOrders->count())
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gold/30">
                            指数
                                <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Side</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Amount (BTC)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Total</th>
                             </thead>
                        <tbody class="divide-y divide-gold/20">
                            @foreach($completedOrders as $order)
                            <tr>
                                <td class="px-4 py-3 text-sm">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-3">{{ strtoupper($order->side) }}</td>
                                <td class="px-4 py-3">{{ number_format($order->filled_amount, 6) }}</td>
                                <td class="px-4 py-3">KES {{ number_format($order->price_per_btc, 2) }}</td>
                                <td class="px-4 py-3">KES {{ number_format($order->filled_kes, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-ivory/50 py-4">No completed orders.</p>
            @endif
        </div>
    </div>
</div>

<!-- TradingView Widget Script -->
<script src="https://s3.tradingview.com/tv.js"></script>
<script>
function tradingManager() {
    return {
        activeOrder: 'buy',
        buyAmount: 0,
        buyOrderType: 'market',
        buyLimitPrice: 0,
        sellAmount: 0,
        sellOrderType: 'market',
        sellLimitPrice: 0,
        currentPrice: {{ $btcPrice }},
        isSubmitting: false,
        orderError: '',
        init() {
            this.initTradingView();
            setInterval(() => this.refreshPrice(), 10000);
        },
        initTradingView() {
            new TradingView.widget({
                "width": "100%",
                "height": 400,
                "symbol": "BITSTAMP:BTCUSD",
                "interval": "15",
                "timezone": "Africa/Nairobi",
                "theme": "dark",
                "style": "1",
                "locale": "en",
                "toolbar_bg": "#f1f3f6",
                "enable_publishing": false,
                "allow_symbol_change": true,
                "container_id": "tradingview-widget"
            });
        },
        get buyTotal() {
            let price = this.buyOrderType === 'market' ? this.currentPrice : this.buyLimitPrice;
            return this.buyAmount * price;
        },
        get sellTotal() {
            let price = this.sellOrderType === 'market' ? this.currentPrice : this.sellLimitPrice;
            return this.sellAmount * price;
        },
        async refreshPrice() {
            try {
                const response = await fetch('/api/trading/price', {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem('token') }
                });
                const data = await response.json();
                this.currentPrice = data.price_kes;
            } catch (e) { console.error(e); }
        },
        async submitOrder(type) {
            this.isSubmitting = true;
            this.orderError = '';
            let data = {};
            if (type === 'buy') {
                data = {
                    amount_btc: this.buyAmount,
                    order_type: this.buyOrderType,
                    limit_price: this.buyOrderType === 'limit' ? this.buyLimitPrice : null
                };
            } else {
                data = {
                    amount_btc: this.sellAmount,
                    order_type: this.sellOrderType,
                    limit_price: this.sellOrderType === 'limit' ? this.sellLimitPrice : null
                };
            }
            try {
                const response = await fetch(`/api/trading/${type}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.error || 'Order failed');
                window.location.reload();
            } catch (err) {
                this.orderError = err.message;
            } finally {
                this.isSubmitting = false;
            }
        },
        formatNumber(num) {
            return num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }
}
</script>
@endsection
