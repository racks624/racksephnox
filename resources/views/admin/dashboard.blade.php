@extends('admin.layouts.app')

@section('content')
<div x-data="adminDashboard()" x-init="init()" class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="admin-card p-5">
            <div class="flex justify-between">
                <div>
                    <p class="text-sm text-gold-400">Total Users</p>
                    <p class="text-3xl font-bold text-gold">{{ number_format($stats['total_users']) }}</p>
                    <p class="text-xs text-green-400">+{{ number_format($stats['new_users_today'] ?? 0) }} today</p>
                </div>
                <i class="fas fa-users text-3xl text-gold/50"></i>
            </div>
        </div>
        <div class="admin-card p-5">
            <div class="flex justify-between">
                <div>
                    <p class="text-sm text-gold-400">Total Invested</p>
                    <p class="text-3xl font-bold text-gold">KES {{ number_format($stats['total_invested'], 2) }}</p>
                </div>
                <i class="fas fa-chart-line text-3xl text-gold/50"></i>
            </div>
        </div>
        <div class="admin-card p-5">
            <div class="flex justify-between">
                <div>
                    <p class="text-sm text-gold-400">Pending Deposits</p>
                    <p class="text-3xl font-bold text-yellow-400">{{ $stats['pending_deposits'] }}</p>
                    <p class="text-xs text-gold-400">KES {{ number_format($stats['pending_deposits_amount'], 2) }}</p>
                </div>
                <i class="fas fa-clock text-3xl text-gold/50"></i>
            </div>
        </div>
        <div class="admin-card p-5">
            <div class="flex justify-between">
                <div>
                    <p class="text-sm text-gold-400">Pending Withdrawals</p>
                    <p class="text-3xl font-bold text-orange-400">{{ $stats['pending_withdrawals'] }}</p>
                    <p class="text-xs text-gold-400">KES {{ number_format($stats['pending_withdrawals_amount'], 2) }}</p>
                </div>
                <i class="fas fa-clock text-3xl text-gold/50"></i>
            </div>
        </div>
    </div>

    <!-- Revenue Target Widget -->
    <div class="admin-card p-5">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gold">💰 26‑Day Revenue Target</h3>
            <span class="text-xs text-ivory/50">{{ $daysLeft }} days left</span>
        </div>
        <div class="flex justify-between items-baseline mb-2">
            <span class="text-2xl font-bold text-gold">KES {{ number_format($revenueTarget->current_revenue, 2) }}</span>
            <span class="text-ivory/60">/ KES {{ number_format($revenueTarget->target_amount, 2) }}</span>
        </div>
        <div class="w-full bg-gray-700 rounded-full h-3 mb-2">
            <div class="bg-gradient-to-r from-gold-500 to-green-500 h-3 rounded-full" style="width: {{ min(100, $targetProgress) }}%"></div>
        </div>
        <p class="text-sm text-ivory/70">Remaining: KES {{ number_format($remaining, 2) }}</p>
        @if($remaining <= 0)
            <p class="text-green-400 text-sm mt-2">🎯 Target achieved! Divine abundance manifested.</p>
        @endif
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="admin-card p-5">
            <h3 class="text-lg font-semibold text-gold mb-4">👥 User Growth (30 days)</h3>
            <canvas id="userGrowthChart" height="200"></canvas>
        </div>
        <div class="admin-card p-5">
            <h3 class="text-lg font-semibold text-gold mb-4">💰 Revenue Trend (30 days)</h3>
            <canvas id="revenueChart" height="200"></canvas>
        </div>
    </div>

    <!-- Lottery Activity Chart -->
    <div class="admin-card p-5">
        <h3 class="text-lg font-semibold text-gold mb-4">🎲 Lottery Activity (30 days)</h3>
        <canvas id="lotteryChart" height="200"></canvas>
    </div>

    <!-- Recent Activities Table -->
    <div class="admin-card p-5">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gold">📋 Recent Platform Activity</h3>
            <button @click="refreshStats" class="text-gold-400 hover:text-gold text-sm">
                <i class="fas fa-sync-alt" :class="{'animate-spin': refreshing}"></i> Refresh
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gold/30">
                    <tr class="text-gold-400">
                        <th class="px-4 py-2 text-left">Time</th>
                        <th class="px-4 py-2 text-left">User</th>
                        <th class="px-4 py-2 text-left">Type</th>
                        <th class="px-4 py-2 text-left">Details</th>
                        <th class="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentActivities as $activity)
                    <tr class="border-b border-gold/20 hover:bg-gold/5">
                        <td class="px-4 py-2 text-ivory/70">{{ $activity['created_at']->diffForHumans() }}</td>
                        <td class="px-4 py-2">{{ $activity['user'] }}</td>
                        <td class="px-4 py-2 uppercase text-xs font-bold">
                            @if($activity['type'] == 'deposit')<span class="text-green-400">Deposit</span>
                            @elseif($activity['type'] == 'withdrawal')<span class="text-orange-400">Withdrawal</span>
                            @else<span class="text-gold-400">Lottery</span>@endif
                        </td>
                        <td class="px-4 py-2">
                            @if($activity['type'] == 'deposit')KES {{ number_format($activity['amount'], 2) }}
                            @elseif($activity['type'] == 'withdrawal')KES {{ number_format($activity['amount'], 2) }}
                            @elseBet: KES {{ number_format($activity['bet'], 2) }} | Win: KES {{ number_format($activity['win'], 2) }}@endif
                        </td>
                        <td class="px-4 py-2">
                            @if(isset($activity['status']))
                                <span class="px-2 py-1 rounded-full text-xs @if($activity['status'] == 'pending') bg-yellow-500/20 text-yellow-400 @elseif($activity['status'] == 'verified' || $activity['status'] == 'completed') bg-green-500/20 text-green-400 @else bg-red-500/20 text-red-400 @endif">{{ ucfirst($activity['status']) }}</span>
                            @else<span class="text-gold-400">Completed</span>@endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function adminDashboard() {
    return {
        refreshing: false,
        userGrowthChart: null,
        revenueChart: null,
        lotteryChart: null,
        init() {
            this.initCharts();
            setInterval(() => this.refreshStats(), 60000);
        },
        initCharts() {
            const ctx1 = document.getElementById('userGrowthChart')?.getContext('2d');
            if(ctx1) new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: {!! json_encode($userGrowth['labels']) !!},
                    datasets: [{ label: 'Total Users', data: {!! json_encode($userGrowth['data']) !!}, borderColor: '#D4AF37', fill: true, tension: 0.4 }]
                },
                options: { responsive: true, plugins: { legend: { labels: { color: '#D4AF37' } } }, scales: { y: { ticks: { color: '#D4AF37' } }, x: { ticks: { color: '#D4AF37' } } } }
            });
            const ctx2 = document.getElementById('revenueChart')?.getContext('2d');
            if(ctx2) new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($revenueTrend['labels']) !!},
                    datasets: [{ label: 'Revenue (KES)', data: {!! json_encode($revenueTrend['data']) !!}, backgroundColor: '#D4AF37', borderRadius: 8 }]
                },
                options: { responsive: true, plugins: { legend: { labels: { color: '#D4AF37' } } }, scales: { y: { ticks: { color: '#D4AF37' } }, x: { ticks: { color: '#D4AF37' } } } }
            });
            const ctx3 = document.getElementById('lotteryChart')?.getContext('2d');
            if(ctx3) new Chart(ctx3, {
                type: 'line',
                data: {
                    labels: {!! json_encode($lotteryActivity['labels']) !!},
                    datasets: [
                        { label: 'Spins', data: {!! json_encode($lotteryActivity['spins']) !!}, borderColor: '#FFD700', yAxisID: 'y' },
                        { label: 'Bet Amount (KES)', data: {!! json_encode($lotteryActivity['bets']) !!}, borderColor: '#D4AF37', backgroundColor: 'rgba(212,175,55,0.2)', fill: true, yAxisID: 'y1' }
                    ]
                },
                options: { responsive: true, interaction: { mode: 'index', intersect: false }, plugins: { legend: { labels: { color: '#D4AF37' } } }, scales: { y: { ticks: { color: '#FFD700' } }, y1: { ticks: { color: '#D4AF37' }, grid: { drawOnChartArea: false } }, x: { ticks: { color: '#D4AF37' } } } }
            });
        },
        async refreshStats() {
            this.refreshing = true;
            try {
                const res = await fetch('{{ route("admin.stats") }}');
                const data = await res.json();
                // Update stats cards dynamically
            } catch(e) { console.error(e); }
            finally { this.refreshing = false; }
        }
    }
}
</script>
@endsection
