@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <h1 class="text-2xl font-bold text-gold mb-4">📊 Trading Analytics</h1>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="admin-card p-4 text-center"><p class="text-ivory/60">Total Orders</p><p class="text-2xl font-bold text-gold">{{ number_format($orderStats['total_orders']) }}</p></div>
                <div class="admin-card p-4 text-center"><p class="text-ivory/60">Total Volume (KES)</p><p class="text-2xl font-bold text-gold">{{ number_format($orderStats['total_volume'], 2) }}</p></div>
                <div class="admin-card p-4 text-center"><p class="text-ivory/60">Average Order (KES)</p><p class="text-2xl font-bold text-gold">{{ number_format($orderStats['avg_order'], 2) }}</p></div>
            </div>
            <div class="admin-card p-5">
                <h3 class="text-lg font-semibold text-gold mb-4">Trading Volume Trend (30 days)</h3>
                <canvas id="tradingVolumeChart" height="200"></canvas>
            </div>
            <div class="admin-card p-5 mt-6">
                <h3 class="text-lg font-semibold text-gold mb-4">🏆 Top Traders</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm"><thead class="border-b border-gold/30"><tr class="text-gold-400"><th class="px-4 py-2">Rank</th><th>User</th><th>Volume (KES)</th></tr></thead>
                    <tbody>@foreach($topTraders as $index => $trader)<tr class="border-b border-gold/20"><td class="px-4 py-2">{{ $index+1 }}</td><td>{{ $trader->name }}</td><td>{{ number_format($trader->trading_orders_sum_amount, 2) }}</td></tr>@endforeach</tbody></table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>new Chart(document.getElementById('tradingVolumeChart'), { type:'line', data:{ labels:{!! json_encode($volumeTrend['labels']) !!}, datasets:[{ label:'Volume (KES)', data:{!! json_encode($volumeTrend['data']) !!}, borderColor:'#D4AF37', fill:true }] } });</script>
@endsection
