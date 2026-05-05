@extends('admin.layouts.app')

@section('content')
<div x-data="reportsManager()" x-init="init()" class="admin-card p-6">
    <h1 class="text-2xl font-bold text-gold mb-6">📊 Divine Analytics & Reports</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="admin-card p-4 text-center"><div class="text-2xl text-gold">👥</div><p class="text-sm text-ivory/60">Total Users</p><p class="text-2xl font-bold text-gold">{{ number_format($totalUsers) }}</p></div>
        <div class="admin-card p-4 text-center"><div class="text-2xl text-gold">✅</div><p class="text-sm text-ivory/60">Verified Users</p><p class="text-2xl font-bold text-gold">{{ number_format($verifiedUsers) }}</p></div>
        <div class="admin-card p-4 text-center"><div class="text-2xl text-gold">💰</div><p class="text-sm text-ivory/60">Total Invested</p><p class="text-2xl font-bold text-gold">KES {{ number_format($totalInvested, 2) }}</p></div>
        <div class="admin-card p-4 text-center"><div class="text-2xl text-gold">📈</div><p class="text-sm text-ivory/60">Trading Volume</p><p class="text-2xl font-bold text-gold">KES {{ number_format($totalTradingVolume, 2) }}</p></div>
        <div class="admin-card p-4 text-center"><div class="text-2xl text-gold">🏦</div><p class="text-sm text-ivory/60">Total Deposits</p><p class="text-2xl font-bold text-gold">KES {{ number_format($totalDeposited, 2) }}</p></div>
        <div class="admin-card p-4 text-center"><div class="text-2xl text-gold">💸</div><p class="text-sm text-ivory/60">Total Withdrawals</p><p class="text-2xl font-bold text-gold">KES {{ number_format($totalWithdrawn, 2) }}</p></div>
        <div class="admin-card p-4 text-center"><div class="text-2xl text-gold">🤖</div><p class="text-sm text-ivory/60">Machine Invested</p><p class="text-2xl font-bold text-gold">KES {{ number_format($totalMachineInvested, 2) }}</p></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="admin-card p-6"><h3 class="text-lg font-semibold text-gold mb-4">📅 Monthly Investments</h3><canvas id="investmentsChart" height="250"></canvas></div>
        <div class="admin-card p-6"><h3 class="text-lg font-semibold text-gold mb-4">👤 User Growth</h3><canvas id="usersChart" height="250"></canvas></div>
        <div class="admin-card p-6 lg:col-span-2"><h3 class="text-lg font-semibold text-gold mb-4">💹 Monthly Trading Volume</h3><canvas id="tradingChart" height="250"></canvas></div>
    </div>

    <div class="admin-card p-6">
        <h3 class="text-lg font-semibold text-gold mb-4">📎 Export Data</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.reports.export.users') }}" class="btn-golden">👥 Users (CSV)</a>
            <a href="{{ route('admin.reports.export.transactions') }}" class="btn-golden">💳 Transactions (CSV)</a>
            <a href="{{ route('admin.reports.export.investments') }}" class="btn-golden">📈 Investments (CSV)</a>
            <a href="{{ route('admin.reports.export.trading') }}" class="btn-golden">📊 Trading (CSV)</a>
            <a href="{{ route('admin.reports.export.pdf') }}" class="btn-golden">📄 PDF Summary</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function reportsManager() {
    return {
        init() {
            new Chart(document.getElementById('investmentsChart'), {
                type: 'bar',
                data: { labels: {!! json_encode($monthlyInvestments->pluck('month')->toArray()) !!}, datasets: [{ label: 'Investment Amount (KES)', data: {!! json_encode($monthlyInvestments->pluck('total')->toArray()) !!}, backgroundColor: '#D4AF37', borderRadius: 8 }] },
                options: { responsive: true }
            });
            new Chart(document.getElementById('usersChart'), {
                type: 'line',
                data: { labels: {!! json_encode($monthlyUsers->pluck('month')->toArray()) !!}, datasets: [{ label: 'New Users', data: {!! json_encode($monthlyUsers->pluck('total')->toArray()) !!}, borderColor: '#D4AF37', fill: true, tension: 0.4 }] }
            });
            new Chart(document.getElementById('tradingChart'), {
                type: 'line',
                data: { labels: {!! json_encode($monthlyTradingVolume->pluck('month')->toArray()) !!}, datasets: [{ label: 'Trading Volume (KES)', data: {!! json_encode($monthlyTradingVolume->pluck('total')->toArray()) !!}, borderColor: '#F5AE23', fill: true, tension: 0.4 }] }
            });
        }
    }
}
</script>
@endsection
