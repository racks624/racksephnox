@extends('layouts.app')
@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4">
        <div class="card-golden p-6">
            <h1 class="text-3xl font-bold golden-title mb-4">₿ Bitcoin Trading Console</h1>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Chart Panel -->
                <div class="lg:col-span-2">
                    <div id="chart" style="height: 400px;"></div>
                    <div class="flex gap-2 mt-2">
                        <button onclick="changeInterval('1m')" class="btn-outline-silver text-xs">1m</button>
                        <button onclick="changeInterval('5m')" class="btn-outline-silver text-xs">5m</button>
                        <button onclick="changeInterval('1h')" class="btn-outline-silver text-xs">1h</button>
                        <button onclick="changeInterval('4h')" class="btn-outline-silver text-xs">4h</button>
                        <button onclick="changeInterval('1d')" class="btn-outline-silver text-xs">1d</button>
                    </div>
                </div>
                <!-- Order Entry Panel -->
                <div>
                    <div class="bg-cosmic-deep/50 rounded-xl p-4">
                        <h3 class="text-gold font-bold mb-2">Place Order</h3>
                        <div class="flex gap-2 mb-2">
                            <button id="buyTab" class="flex-1 py-2 bg-green-500/20 text-green-400 rounded">BUY</button>
                            <button id="sellTab" class="flex-1 py-2 bg-red-500/20 text-red-400 rounded">SELL</button>
                        </div>
                        <div id="buyForm">
                            <form method="POST" action="{{ route('trading.buy') }}">
                                @csrf
                                <div><label>Amount (BTC)</label><input type="number" step="0.0001" name="amount_btc" class="input-golden w-full" required></div>
                                <div><label>Order Type</label><select name="order_type" class="input-golden w-full"><option value="market">Market</option><option value="limit">Limit</option><option value="stop">Stop</option></select></div>
                                <div class="limit-fields hidden"><label>Price (KES)</label><input type="number" name="price" class="input-golden w-full"></div>
                                <div><label>Take Profit (KES)</label><input type="number" name="take_profit" class="input-golden w-full"></div>
                                <div><label>Stop Loss (KES)</label><input type="number" name="stop_loss" class="input-golden w-full"></div>
                                <button type="submit" class="btn-golden w-full mt-3">Buy BTC</button>
                            </form>
                        </div>
                        <div id="sellForm" class="hidden">
                            <form method="POST" action="{{ route('trading.sell') }}">@csrf @method('POST')
                                <div><label>Amount (BTC)</label><input type="number" step="0.0001" name="amount_btc" class="input-golden w-full" required></div>
                                <div><label>Order Type</label><select name="order_type" class="input-golden w-full"><option value="market">Market</option><option value="limit">Limit</option><option value="stop">Stop</option></select></div>
                                <div class="limit-fields hidden"><label>Price (KES)</label><input type="number" name="price" class="input-golden w-full"></div>
                                <div><label>Take Profit (KES)</label><input type="number" name="take_profit" class="input-golden w-full"></div>
                                <div><label>Stop Loss (KES)</label><input type="number" name="stop_loss" class="input-golden w-full"></div>
                                <button type="submit" class="btn-golden w-full mt-3">Sell BTC</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Order Book & Open Orders -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div><h3 class="text-gold">📖 Order Book</h3><div id="orderBook" class="h-64 overflow-y-auto text-sm"></div></div>
                <div><h3 class="text-gold">⏳ Open Orders</h3>@if($openOrders->count())<div class="space-y-2">@foreach($openOrders as $order)<div class="flex justify-between p-2 bg-gold/5 rounded"><span>{{ ucfirst($order->side) }} {{ $order->amount_btc }} BTC @ KES {{ number_format($order->limit_price ?? 0) }}</span><form action="{{ route('trading.cancel', $order) }}" method="POST">@csrf<button class="text-red-400 text-sm">Cancel</button></form></div>@endforeach</div>@else<p>No open orders</p>@endif</div>
            </div>
        </div>
    </div>
</div>
<script src="https://unpkg.com/lightweight-charts/dist/lightweight-charts.standalone.production.js"></script>
<script>
    let chart, candleSeries, currentInterval = '1h';
    function initChart() { chart = LightweightCharts.createChart(document.getElementById('chart'), { width: 800, height: 400, layout: { background: { color: '#0F172A' }, textColor: '#D4AF37' }, grid: { vertLines: { color: '#334155' }, horzLines: { color: '#334155' } } }); candleSeries = chart.addCandlestickSeries({ upColor: '#26A69A', downColor: '#EF5350', borderVisible: false, wickUpColor: '#26A69A', wickDownColor: '#EF5350' }); fetchCandles(); }
    async function fetchCandles() { const res = await fetch(`/trading/candles/${currentInterval}`); const data = await res.json(); const candles = data.map(c => ({ time: new Date(c.open_time).getTime() / 1000, open: parseFloat(c.open), high: parseFloat(c.high), low: parseFloat(c.low), close: parseFloat(c.close) })); candleSeries.setData(candles); }
    function changeInterval(interval) { currentInterval = interval; fetchCandles(); }
    function fetchOrderBook() { fetch('/trading/order-book').then(r=>r.json()).then(data => { let html = '<div class="flex justify-between"><span>Price (KES)</span><span>Amount (BTC)</span></div>'; data.asks.slice(0,10).forEach(a => html += `<div class="flex justify-between text-red-400"><span>${a.price}</span><span>${a.amount}</span></div>`); data.bids.slice(0,10).forEach(b => html += `<div class="flex justify-between text-green-400"><span>${b.price}</span><span>${b.amount}</span></div>`); document.getElementById('orderBook').innerHTML = html; }); }
    document.getElementById('buyTab').addEventListener('click', () => { document.getElementById('buyForm').classList.remove('hidden'); document.getElementById('sellForm').classList.add('hidden'); });
    document.getElementById('sellTab').addEventListener('click', () => { document.getElementById('buyForm').classList.add('hidden'); document.getElementById('sellForm').classList.remove('hidden'); });
    document.querySelectorAll('select[name="order_type"]').forEach(sel => sel.addEventListener('change', function() { this.closest('form').querySelectorAll('.limit-fields').forEach(el => el.classList.toggle('hidden', this.value !== 'limit')); }));
    initChart(); setInterval(fetchOrderBook, 5000);
</script>
@endsection
