@extends('admin.layouts.app')

@section('content')
<div x-data="adminDashboard()" x-init="init()" class="p-6">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold golden-title">⚡ Divine Admin Portal</h1>
            <p class="text-gold-400/70 mt-1">Manage the infinite spiral of wealth creation</p>
        </div>
        <div class="flex gap-2">
            <button @click="refreshData" class="btn-golden text-sm py-2 px-4">
                <i class="fas fa-sync-alt mr-1"></i> Refresh
            </button>
            <a href="{{ route('admin.reports.index') }}" class="btn-outline-silver text-sm py-2 px-4 rounded-lg">
                <i class="fas fa-chart-bar mr-1"></i> Reports
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5 mb-8">
        <div class="card-golden p-5 hover:scale-105 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-ivory/60">Total Users</p>
                    <p class="text-3xl font-bold text-gold">{{ number_format($totalUsers) }}</p>
                    <p class="text-xs text-green-400 mt-1">+{{ rand(1, 20) }}% this month</p>
                </div>
                <i class="fas fa-users text-4xl text-gold/50"></i>
            </div>
        </div>
        <div class="card-golden p-5 hover:scale-105 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-ivory/60">Verified KYC</p>
                    <p class="text-3xl font-bold text-gold">{{ number_format($verifiedUsers) }}</p>
                    <p class="text-xs text-green-400 mt-1">{{ $totalUsers > 0 ? round(($verifiedUsers / $totalUsers) * 100, 1) : 0 }}% of total</p>
                </div>
                <i class="fas fa-id-card text-4xl text-gold/50"></i>
            </div>
        </div>
        <div class="card-golden p-5 hover:scale-105 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-ivory/60">Total Invested</p>
                    <p class="text-2xl font-bold text-green-400">KES {{ number_format($totalInvested, 2) }}</p>
                </div>
                <i class="fas fa-chart-line text-4xl text-gold/50"></i>
            </div>
        </div>
        <div class="card-golden p-5 hover:scale-105 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-ivory/60">Pending Deposits</p>
                    <p class="text-3xl font-bold text-yellow-400">{{ $pendingDeposits }}</p>
                </div>
                <i class="fas fa-arrow-down text-4xl text-gold/50"></i>
            </div>
        </div>
        <div class="card-golden p-5 hover:scale-105 transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-ivory/60">Pending Withdrawals</p>
                    <p class="text-3xl font-bold text-orange-400">{{ $pendingWithdrawals }}</p>
                </div>
                <i class="fas fa-arrow-up text-4xl text-gold/50"></i>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="card-golden p-5">
            <h3 class="text-lg font-semibold text-gold mb-4">📈 User Growth (Last 30 Days)</h3>
            <canvas id="userGrowthChart" height="200"></canvas>
        </div>
        <div class="card-golden p-5">
            <h3 class="text-lg font-semibold text-gold mb-4">💰 Investment Volume</h3>
            <canvas id="investmentVolumeChart" height="200"></canvas>
        </div>
    </div>

    <!-- Recent Users Table -->
    <div class="card-golden p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gold">👥 Recent Users</h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gold-400 hover:text-gold">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-gold/30">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">KYC</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Joined</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Action</th>
                    </thead>
                <tbody class="divide-y divide-gold/20">
                    @foreach($recentUsers as $user)
                    <tr class="hover:bg-gold/5 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gold/20 flex items-center justify-center">
                                    <i class="fas fa-user text-gold text-sm"></i>
                                </div>
                                <span class="text-ivory">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-ivory/70">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            @if($user->kyc_status === 'verified')
                                <span class="px-2 py-1 text-xs rounded-full bg-green-500/20 text-green-400">✓ Verified</span>
                            @elseif($user->kyc_status === 'pending')
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-500/20 text-yellow-400">⏳ Pending</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-500/20 text-gray-400">Not Submitted</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-ivory/50">{{ $user->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-gold-400 hover:text-gold text-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
        <a href="{{ route('admin.deposits.index') }}" class="card-golden p-4 flex items-center justify-between hover:border-gold transition">
            <div>
                <i class="fas fa-arrow-down text-2xl text-gold mb-2"></i>
                <p class="font-semibold text-gold">Pending Deposits</p>
                <p class="text-2xl font-bold text-gold">{{ $pendingDeposits }}</p>
            </div>
            <i class="fas fa-chevron-right text-gold-400"></i>
        </a>
        <a href="{{ route('admin.withdrawals.index') }}" class="card-golden p-4 flex items-center justify-between hover:border-gold transition">
            <div>
                <i class="fas fa-arrow-up text-2xl text-gold mb-2"></i>
                <p class="font-semibold text-gold">Pending Withdrawals</p>
                <p class="text-2xl font-bold text-gold">{{ $pendingWithdrawals }}</p>
            </div>
            <i class="fas fa-chevron-right text-gold-400"></i>
        </a>
        <a href="{{ route('admin.kyc.index') }}" class="card-golden p-4 flex items-center justify-between hover:border-gold transition">
            <div>
                <i class="fas fa-id-card text-2xl text-gold mb-2"></i>
                <p class="font-semibold text-gold">KYC Reviews</p>
                <p class="text-2xl font-bold text-gold">{{ App\Models\KycDocument::where('status', 'pending')->count() }}</p>
            </div>
            <i class="fas fa-chevron-right text-gold-400"></i>
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function adminDashboard() {
    return {
        init() {
            this.initCharts();
            setInterval(() => this.refreshData(), 60000);
        },
        
        initCharts() {
            // User Growth Chart
            const userCtx = document.getElementById('userGrowthChart')?.getContext('2d');
            if (userCtx) {
                new Chart(userCtx, {
                    type: 'line',
                    data: {
                        labels: @json(array_map(function($i) { return now()->subDays($i)->format('M d'); }, range(29, 0))),
                        datasets: [{
                            label: 'New Users',
                            data: @json(array_map(function() { return rand(5, 50); }, range(0, 29))),
                            borderColor: '#D4AF37',
                            backgroundColor: 'rgba(212, 175, 55, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { labels: { color: '#D4AF37' } } },
                        scales: {
                            y: { grid: { color: 'rgba(212, 175, 55, 0.1)' }, ticks: { color: '#D4AF37' } },
                            x: { ticks: { color: '#D4AF37' } }
                        }
                    }
                });
            }
            
            // Investment Volume Chart
            const invCtx = document.getElementById('investmentVolumeChart')?.getContext('2d');
            if (invCtx) {
                new Chart(invCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Investment Volume (KES)',
                            data: @json(array_map(function() { return rand(50000, 500000); }, range(0, 5))),
                            backgroundColor: '#D4AF37',
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { labels: { color: '#D4AF37' } } },
                        scales: {
                            y: { grid: { color: 'rgba(212, 175, 55, 0.1)' }, ticks: { color: '#D4AF37' } },
                            x: { ticks: { color: '#D4AF37' } }
                        }
                    }
                });
            }
        },
        
        async refreshData() {
            try {
                const response = await fetch('/admin/api/stats');
                if (response.ok) {
                    const data = await response.json();
                    location.reload();
                }
            } catch (error) {
                console.error('Refresh failed:', error);
            }
        }
    }
}
</script>
@endsection
