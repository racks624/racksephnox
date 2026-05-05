@extends('admin.layouts.app')

@section('content')
<div x-data="monitoringDashboard()" x-init="init()" class="space-y-6">
    <h1 class="text-2xl font-bold text-gold">📡 System Monitoring</h1>

    <!-- Health Status -->
    <div class="admin-card p-5">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gold">System Health</h2>
            <span class="px-3 py-1 rounded-full text-sm" :class="health.status === 'operational' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'" x-text="health.status"></span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div><p class="text-ivory/60">Response Time</p><p class="text-2xl font-bold text-gold" x-text="health.metrics?.response_time + 's'"></p></div>
            <div><p class="text-ivory/60">Error Rate</p><p class="text-2xl font-bold text-orange-400" x-text="(health.metrics?.error_rate * 100).toFixed(3) + '%'"></p></div>
            <div><p class="text-ivory/60">Queue Size</p><p class="text-2xl font-bold text-gold" x-text="health.metrics?.queue_size"></p></div>
            <div><p class="text-ivory/60">Failed Jobs</p><p class="text-2xl font-bold text-red-400" x-text="health.metrics?.failed_jobs"></p></div>
        </div>
    </div>

    <!-- Daily Revenue Summary -->
    <div class="admin-card p-5">
        <h2 class="text-xl font-bold text-gold mb-4">💰 Daily Revenue Summary</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div><p class="text-ivory/60">Deposits</p><p class="text-2xl font-bold text-green-400" x-text="'KES ' + formatNumber(revenue.total_deposits)"></p></div>
            <div><p class="text-ivory/60">Withdrawals</p><p class="text-2xl font-bold text-red-400" x-text="'KES ' + formatNumber(revenue.total_withdrawals)"></p></div>
            <div><p class="text-ivory/60">Lottery Tax</p><p class="text-2xl font-bold text-gold" x-text="'KES ' + formatNumber(revenue.lottery_tax)"></p></div>
            <div><p class="text-ivory/60">Trading Volume</p><p class="text-2xl font-bold text-gold" x-text="'KES ' + formatNumber(revenue.trading_volume)"></p></div>
            <div><p class="text-ivory/60">Machine Investments</p><p class="text-2xl font-bold text-gold" x-text="'KES ' + formatNumber(revenue.machine_investments)"></p></div>
            <div><p class="text-ivory/60">Net Revenue</p><p class="text-2xl font-bold text-gold" x-text="'KES ' + formatNumber(revenue.net_revenue)"></p></div>
        </div>
    </div>

    <!-- Refresh Button -->
    <div class="flex justify-end">
        <button @click="refresh()" :disabled="refreshing" class="btn-golden">
            <i class="fas fa-sync-alt" :class="{'animate-spin': refreshing}"></i> Refresh Metrics
        </button>
    </div>
</div>

<script>
function monitoringDashboard() {
    return {
        health: {},
        revenue: {},
        refreshing: false,
        init() {
            this.refresh();
            setInterval(() => this.refresh(), 30000);
        },
        async refresh() {
            this.refreshing = true;
            try {
                const [healthRes, revenueRes] = await Promise.all([
                    fetch('{{ route("admin.monitoring.health") }}'),
                    fetch('{{ route("admin.monitoring.revenue") }}')
                ]);
                this.health = await healthRes.json();
                this.revenue = await revenueRes.json();
            } catch(e) { console.error(e); }
            finally { this.refreshing = false; }
        },
        formatNumber(num) { return num?.toLocaleString() ?? '0'; }
    }
}
</script>
@endsection
