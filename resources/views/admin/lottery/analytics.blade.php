@extends('admin.layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gold">📈 Lottery Analytics</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="admin-card p-4"><p class="text-ivory/60">Current RTP (30d)</p><p class="text-2xl font-bold text-gold">{{ $game ? round(($rtpChart['wins']->sum() / max(1, $rtpChart['bets']->sum())) * 100, 2) : 0 }}%</p></div>
        <div class="admin-card p-4"><p class="text-ivory/60">Total Spins (30d)</p><p class="text-2xl font-bold text-gold">{{ $rtpChart['bets']->sum() }}</p></div>
        <div class="admin-card p-4"><p class="text-ivory/60">Jackpot Growth (30d)</p><p class="text-2xl font-bold text-gold">KES {{ number_format($jackpotHistory->sum('daily_contribution'), 2) }}</p></div>
    </div>

    <div class="admin-card p-5">
        <h3 class="text-lg font-bold text-gold mb-4">RTP Trend (Daily)</h3>
        <canvas id="rtpChart" height="200"></canvas>
    </div>

    <div class="admin-card p-5">
        <h3 class="text-lg font-bold text-gold mb-4">Hourly Spin Distribution (Last 7 days)</h3>
        <canvas id="hourlyChart" height="200"></canvas>
    </div>

    <div class="admin-card p-5">
        <h3 class="text-lg font-bold text-gold mb-4">🏆 Top 10 Winners (All Time)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gold/30"><tr><th class="px-4 py-2">Rank</th><th>User</th><th>Total Won (KES)</th></tr></thead>
                <tbody>
                    @foreach($topUsers as $index => $user)
                    <tr><td class="px-4 py-2">{{ $index+1 }}<td><td class="text-ivory">{{ $user->user->name }}</td><td class="text-green-400">{{ number_format($user->total_win, 2) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const rtpCtx = document.getElementById('rtpChart').getContext('2d');
    new Chart(rtpCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($rtpChart['labels']) !!},
            datasets: [
                { label: 'Total Bets (KES)', data: {!! json_encode($rtpChart['bets']) !!}, borderColor: '#D4AF37', fill: false },
                { label: 'Total Wins (KES)', data: {!! json_encode($rtpChart['wins']) !!}, borderColor: '#00FF88', fill: false }
            ]
        }
    });
    const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
    new Chart(hourlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($hourlySpins->pluck('hour')) !!},
            datasets: [{ label: 'Spins', data: {!! json_encode($hourlySpins->pluck('count')) !!}, backgroundColor: '#D4AF37' }]
        }
    });
</script>
@endsection
