@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <h1 class="text-2xl font-bold text-gold mb-4">🤖 Machine Investment Analytics</h1>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="admin-card p-4 text-center"><p class="text-ivory/60">Total Invested</p><p class="text-2xl font-bold text-gold">KES {{ number_format($totalInvested, 2) }}</p></div>
                <div class="admin-card p-4 text-center"><p class="text-ivory/60">Active Investments</p><p class="text-2xl font-bold text-gold">{{ number_format($activeInvestments) }}</p></div>
                <div class="admin-card p-4 text-center"><p class="text-ivory/60">Total Returns</p><p class="text-2xl font-bold text-green-400">KES {{ number_format($totalReturns, 2) }}</p></div>
            </div>
            <div class="admin-card p-5">
                <h3 class="text-lg font-semibold text-gold mb-4">Investment Trend (30 days)</h3>
                <canvas id="investmentTrendChart" height="200"></canvas>
            </div>
            <div class="admin-card p-5 mt-6">
                <h3 class="text-lg font-semibold text-gold mb-4">🔥 Top Machines</h3>
                <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="border-b border-gold/30"><tr class="text-gold-400"><th>Rank</th><th>Machine</th><th>Total Invested (KES)</th></tr></thead>
                <tbody>@foreach($topMachines as $index => $machine)<tr class="border-b border-gold/20"><td>{{ $index+1 }}</td><td>{{ $machine->name }}</td><td>{{ number_format($machine->investments_sum_amount, 2) }}</td></tr>@endforeach</tbody></table></div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>new Chart(document.getElementById('investmentTrendChart'), { type:'line', data:{ labels:{!! json_encode($investmentTrend['labels']) !!}, datasets:[{ label:'Investment (KES)', data:{!! json_encode($investmentTrend['data']) !!}, borderColor:'#D4AF37', fill:true }] } });</script>
@endsection
