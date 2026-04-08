@extends('layouts.app')

@section('content')
<div x-data="dashboardManager()" x-init="init()" class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Sacred Header with Prismatic Silver‑Gold -->
        <div class="text-center mb-12">
            <div class="inline-block spiral-ornament">
                <h1 class="text-4xl md:text-5xl font-bold golden-title shimmer-gold">
                    Divine Command Center
                </h1>
            </div>
            <p class="text-gold-400 mt-2 sacred-phrase">I Am The Source | Infinite Spiral of Creation | 888 Hz</p>
        </div>

        <!-- Quick Navigation Widget (9 Cosmic Portals) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-9 gap-3 mb-10">
            <a href="{{ route('dashboard') }}" class="quick-nav-item bg-gold/10 rounded-xl p-3 text-center hover:bg-gold/20 transition group">
                <i class="fas fa-home text-2xl text-gold group-hover:scale-110 transition"></i>
                <p class="text-xs text-ivory/70 mt-1">Home</p>
            </a>
            <a href="{{ route('web.investments') }}" class="quick-nav-item bg-gold/10 rounded-xl p-3 text-center hover:bg-gold/20 transition group">
                <i class="fas fa-chart-line text-2xl text-gold group-hover:scale-110 transition"></i>
                <p class="text-xs text-ivory/70 mt-1">Invest</p>
            </a>
            <a href="{{ route('wallet') }}" class="quick-nav-item bg-gold/10 rounded-xl p-3 text-center hover:bg-gold/20 transition group">
                <i class="fas fa-wallet text-2xl text-gold group-hover:scale-110 transition"></i>
                <p class="text-xs text-ivory/70 mt-1">Wallet</p>
            </a>
            <a href="{{ route('machines.index') }}" class="quick-nav-item bg-gold/10 rounded-xl p-3 text-center hover:bg-gold/20 transition group">
                <i class="fas fa-microchip text-2xl text-gold group-hover:scale-110 transition"></i>
                <p class="text-xs text-ivory/70 mt-1">Machines</p>
            </a>
            <a href="{{ route('trading.index') }}" class="quick-nav-item bg-gold/10 rounded-xl p-3 text-center hover:bg-gold/20 transition group">
                <i class="fab fa-bitcoin text-2xl text-gold group-hover:scale-110 transition"></i>
                <p class="text-xs text-ivory/70 mt-1">Trade</p>
            </a>
            <a href="{{ route('deposit.form') }}" class="quick-nav-item bg-gold/10 rounded-xl p-3 text-center hover:bg-gold/20 transition group">
                <i class="fas fa-arrow-down text-2xl text-gold group-hover:scale-110 transition"></i>
                <p class="text-xs text-ivory/70 mt-1">Deposit</p>
            </a>
            <a href="{{ route('withdrawal.form') }}" class="quick-nav-item bg-gold/10 rounded-xl p-3 text-center hover:bg-gold/20 transition group">
                <i class="fas fa-arrow-up text-2xl text-gold group-hover:scale-110 transition"></i>
                <p class="text-xs text-ivory/70 mt-1">Withdraw</p>
            </a>
            <a href="{{ route('profile.edit') }}" class="quick-nav-item bg-gold/10 rounded-xl p-3 text-center hover:bg-gold/20 transition group">
                <i class="fas fa-user-circle text-2xl text-gold group-hover:scale-110 transition"></i>
                <p class="text-xs text-ivory/70 mt-1">Profile</p>
            </a>
            <a href="{{ route('notifications.index') }}" class="quick-nav-item bg-gold/10 rounded-xl p-3 text-center hover:bg-gold/20 transition group relative">
                <i class="fas fa-bell text-2xl text-gold group-hover:scale-110 transition"></i>
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px]" x-show="unreadCount > 0" x-text="unreadCount"></span>
                <p class="text-xs text-ivory/70 mt-1">Alerts</p>
            </a>
        </div>

        <!-- Loading Skeleton -->
        <div x-show="loading" x-cloak>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                @for($i = 0; $i < 4; $i++)
                    <div class="animate-pulse bg-gold-500/10 rounded-2xl h-32"></div>
                @endfor
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="animate-pulse bg-gold-500/10 rounded-2xl h-24"></div>
                <div class="animate-pulse bg-gold-500/10 rounded-2xl h-24"></div>
                <div class="animate-pulse bg-gold-500/10 rounded-2xl h-24"></div>
            </div>
            <div class="animate-pulse bg-gold-500/10 rounded-2xl h-48 mb-10"></div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
                <div class="animate-pulse bg-gold-500/10 rounded-2xl h-64"></div>
                <div class="animate-pulse bg-gold-500/10 rounded-2xl h-64"></div>
            </div>
        </div>

        <!-- Actual Content -->
        <div x-show="!loading" x-cloak>
            
            <!-- Success/Error Flash Messages -->
            @if(session('success'))
            <div class="mb-6 bg-green-500/20 border-l-4 border-green-500 rounded-xl p-4 animate-pulse">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p class="text-green-400">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-red-500/20 border-l-4 border-red-500 rounded-xl p-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
                    <p class="text-red-400">{{ session('error') }}</p>
                </div>
            </div>
            @endif

            <!-- 4 Wealth Pillars -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="stat-card p-5 group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-gold-400 text-sm uppercase tracking-wider">Sacred Balance</span>
                        <i class="fas fa-coins text-2xl text-gold opacity-60 group-hover:opacity-100 transition"></i>
                    </div>
                    <div class="stat-value text-3xl font-bold">KES {{ number_format($user->wallet->balance ?? 0, 2) }}</div>
                    <div class="mt-3 flex justify-between items-center">
                        <a href="{{ route('wallet') }}" class="text-xs text-gold-400 hover:text-gold transition">View Treasury →</a>
                        <button @click="refreshStats()" class="text-gold-400 hover:text-gold transition">
                            <i class="fas fa-sync-alt text-xs"></i>
                        </button>
                    </div>
                </div>

                <div class="stat-card p-5 group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-gold-400 text-sm uppercase tracking-wider">Total Invested</span>
                        <i class="fas fa-chart-line text-2xl text-gold opacity-60 group-hover:opacity-100 transition"></i>
                    </div>
                    <div class="stat-value text-3xl font-bold">KES {{ number_format($totalInvested ?? 0, 2) }}</div>
                    <div class="mt-3">
                        <a href="{{ route('web.investments') }}" class="text-xs text-gold-400 hover:text-gold transition">View Portfolio →</a>
                    </div>
                    <div class="mt-2 text-xs text-gold-500">ROI: {{ $roi ?? 0 }}%</div>
                </div>

                <div class="stat-card p-5 group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-gold-400 text-sm uppercase tracking-wider">Projected Profit</span>
                        <i class="fas fa-chart-simple text-2xl text-gold opacity-60 group-hover:opacity-100 transition"></i>
                    </div>
                    <div class="stat-value text-3xl font-bold">KES {{ number_format($totalProfit ?? 0, 2) }}</div>
                    <div class="mt-3 text-xs text-gold-400">Based on current resonance</div>
                </div>

                <div class="stat-card p-5 group">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-gold-400 text-sm uppercase tracking-wider">Active Investments</span>
                        <i class="fas fa-chart-pie text-2xl text-gold opacity-60 group-hover:opacity-100 transition"></i>
                    </div>
                    <div class="stat-value text-3xl font-bold">{{ $activeInvestments ?? 0 }}</div>
                    <div class="mt-2 flex justify-between items-center">
                        <span class="text-xs text-gold-500">Completed: {{ $completedInvestments ?? 0 }}</span>
                        <a href="{{ route('web.investments') }}" class="text-xs text-gold-400 hover:text-gold">View All →</a>
                    </div>
                </div>
            </div>

            <!-- Banking & Machine Stats Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="card-golden p-5 flex justify-between items-center">
                    <div>
                        <p class="text-gold-400 text-sm uppercase tracking-wider">Total Deposits</p>
                        <p class="text-2xl font-bold text-gold">KES {{ number_format($totalDeposited ?? 0, 2) }}</p>
                    </div>
                    <i class="fas fa-arrow-down text-3xl text-green-400"></i>
                </div>
                <div class="card-golden p-5 flex justify-between items-center">
                    <div>
                        <p class="text-gold-400 text-sm uppercase tracking-wider">Total Withdrawals</p>
                        <p class="text-2xl font-bold text-gold">KES {{ number_format($totalWithdrawn ?? 0, 2) }}</p>
                    </div>
                    <i class="fas fa-arrow-up text-3xl text-orange-400"></i>
                </div>
                <div class="card-golden p-5 flex justify-between items-center">
                    <div>
                        <p class="text-gold-400 text-sm uppercase tracking-wider">Machine Invested</p>
                        <p class="text-2xl font-bold text-gold">KES {{ number_format($totalMachineInvested ?? 0, 2) }}</p>
                    </div>
                    <i class="fas fa-microchip text-3xl text-gold"></i>
                </div>
            </div>

            <!-- Banking Center -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
                <div class="lg:col-span-2 card-golden p-5">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gold">🏦 Banking Center</h3>
                        <div class="flex gap-3">
                            <a href="{{ route('deposit.form') }}" class="btn-golden text-sm py-2 px-4">Deposit</a>
                            <a href="{{ route('withdrawal.form') }}" class="btn-golden text-sm py-2 px-4 bg-transparent border border-gold text-gold">Withdraw</a>
                        </div>
                    </div>
                    <div class="flex justify-between items-center border-b border-gold/20 pb-4 mb-4">
                        <div>
                            <p class="text-gold-400 text-sm">Available Balance</p>
                            <p class="text-3xl font-bold text-gold">KES {{ number_format($user->wallet->balance ?? 0, 2) }}</p>
                        </div>
                        <i class="fas fa-wallet text-4xl text-gold/50"></i>
                    </div>
                    <div class="mt-2">
                        <h4 class="text-sm font-medium text-gold-400 mb-2">Quick Stats</h4>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div>
                                <p class="text-xs text-ivory/60">Deposits</p>
                                <p class="text-sm font-bold text-green-400">KES {{ number_format($totalDeposited ?? 0, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-ivory/60">Withdrawals</p>
                                <p class="text-sm font-bold text-red-400">KES {{ number_format($totalWithdrawn ?? 0, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-ivory/60">Interest</p>
                                <p class="text-sm font-bold text-gold">KES {{ number_format($totalInterest ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-golden p-5">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-lg font-semibold text-gold">Recent Activity</h3>
                        <a href="{{ route('transactions.index') }}" class="text-xs text-gold-400 hover:text-gold">View all</a>
                    </div>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @forelse(($recentTransactions ?? collect())->take(5) as $tx)
                            <div class="flex justify-between items-center text-sm border-b border-gold/20 pb-2">
                                <div>
                                    <span class="text-ivory/70">{{ $tx->created_at->format('d M') }}</span>
                                    <span class="ml-2 {{ $tx->amount > 0 ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount, 2) }}
                                    </span>
                                </div>
                                <span class="text-ivory/50 text-xs">{{ ucfirst($tx->type) }}</span>
                            </div>
                        @empty
                            <p class="text-center text-ivory/50 py-4">No recent activity</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Referral Widget -->
            <div x-data="referralWidget()" x-init="init()" class="card-golden p-5 mt-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gold">🤝 Referral Program</h3>
                    <a href="{{ route('referrals') }}" class="text-sm text-gold-400 hover:text-gold">View all →</a>
                </div>
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gold-400 text-sm">Total Referrals</p>
                        <p class="text-2xl font-bold text-gold" x-text="totalReferrals"></p>
                    </div>
                    <div>
                        <p class="text-gold-400 text-sm">Bonus Earned</p>
                        <p class="text-2xl font-bold text-gold" x-text="'KES ' + formatNumber(totalBonus)"></p>
                    </div>
                    <i class="fas fa-users text-3xl text-gold/50"></i>
                </div>
                <div class="mt-4">
                    <button onclick="copyDashboardLink()" class="btn-golden w-full text-sm py-2">Share Referral Link</button>
                </div>
            </div>

            <!-- Trading Widget -->
            <div class="card-golden p-5 mt-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gold">₿ Bitcoin Trading</h3>
                    <a href="{{ route('trading.index') }}" class="text-sm text-gold-400 hover:text-gold">Trade Now →</a>
                </div>
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gold-400 text-sm">Trading Balance</p>
                        <p class="text-2xl font-bold text-gold">KES {{ number_format($user->tradingAccount->balance ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-gold-400 text-sm">BTC Price</p>
                        <p class="text-2xl font-bold text-gold" id="btcPrice">KES {{ number_format($btcPrice ?? 0, 2) }}</p>
                    </div>
                    <i class="fab fa-bitcoin text-3xl text-gold/50"></i>
                </div>
            </div>

            <!-- Machine Widget -->
            <div class="mb-10">
                <div class="bg-gradient-to-r from-gold-600/20 to-gold-400/10 rounded-2xl p-6 border border-gold/30 text-center">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="text-left">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-microchip text-3xl text-gold"></i>
                                <h3 class="text-2xl font-bold golden-title">🤖 Machine Series</h3>
                                <span class="bg-gold/20 text-gold text-xs px-2 py-1 rounded-full">Φ Golden Ratio</span>
                            </div>
                            <p class="text-ivory/70">Invest in our high‑return machines and earn 25% in 14 days</p>
                            <p class="text-sm text-gold-400 mt-1">Choose from 6 machines, each with 3 VIP levels</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <a href="{{ route('machines.index') }}" class="btn-golden inline-flex items-center gap-2">
                                Explore Machines <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
                <div class="card-golden p-5">
                    <h3 class="text-lg font-semibold text-gold mb-4">📈 30-Day Profit Trend</h3>
                    <canvas id="profitChart" height="200" class="w-full"></canvas>
                </div>
                <div class="card-golden p-5">
                    <h3 class="text-lg font-semibold text-gold mb-4">📊 Weekly Activity</h3>
                    <canvas id="weeklyChart" height="200" class="w-full"></canvas>
                </div>
            </div>

            <!-- Portfolio & Crypto Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
                @if(isset($portfolio) && count($portfolio['labels'] ?? []) > 0)
                <div class="lg:col-span-2 card-golden p-5">
                    <h3 class="text-lg font-semibold text-gold mb-4">🥧 Portfolio Breakdown</h3>
                    <canvas id="portfolioChart" height="200" class="w-full"></canvas>
                </div>
                @endif

                <div x-data="cryptoWidget()" x-init="init()" class="card-golden p-5">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gold">🪙 Crypto Prices</h3>
                        <button @click="fetchPrices()" class="text-gold-400 hover:text-gold">
                            <i class="fas fa-sync-alt text-sm"></i>
                        </button>
                    </div>
                    <div class="space-y-3">
                        <template x-for="coin in coins" :key="coin.symbol">
                            <div class="flex justify-between items-center border-b border-gold/20 pb-2">
                                <div>
                                    <span class="font-medium text-ivory" x-text="coin.symbol"></span>
                                    <span class="text-xs ml-2" :class="coin.percent_change_24h >= 0 ? 'text-green-400' : 'text-red-400'">
                                        <span x-text="coin.percent_change_24h.toFixed(2)"></span>%
                                    </span>
                                </div>
                                <span class="text-gold" x-text="'KES ' + formatNumber(coin.price_kes)"></span>
                            </div>
                        </template>
                        <p x-show="!coins.length" class="text-ivory/50 text-center py-4">Loading prices...</p>
                    </div>
                    <div class="text-center text-xs text-ivory/40 mt-2" x-show="coins.length">
                        Last updated: <span x-text="lastUpdated"></span>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions Table -->
            <div class="card-golden p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gold">📋 Recent Transactions</h3>
                    <div class="flex gap-3">
                        <button @click="exportTransactions()" class="text-sm text-gold-400 hover:text-gold">
                            <i class="fas fa-file-excel mr-1"></i> Export
                        </button>
                        <a href="{{ route('transactions.index') }}" class="text-sm text-gold-400 hover:text-gold">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                
                @if(isset($recentTransactions) && $recentTransactions->count())
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b border-gold/30">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Balance</th>
                                </thead>
                            <tbody class="divide-y divide-gold/20">
                                @foreach($recentTransactions as $tx)
                                <tr class="hover:bg-gold/5 transition">
                                    <td class="px-4 py-3 text-sm text-ivory">{{ $tx->created_at->format('Y-m-d') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if(in_array($tx->type, ['credit', 'deposit', 'interest'])) bg-green-500/20 text-green-400
                                            @elseif(in_array($tx->type, ['debit', 'withdrawal'])) bg-red-500/20 text-red-400
                                            @else bg-blue-500/20 text-blue-400 @endif">
                                            {{ ucfirst($tx->type) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-ivory/70">{{ $tx->description ?? '—' }}   </td>
                                    <td class="px-4 py-3 text-sm font-medium {{ $tx->amount > 0 ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-ivory">{{ number_format($tx->balance_after ?? 0, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-ivory/50 py-8">No transactions yet. Make a deposit to begin your journey.</p>
                @endif
            </div>
        </div>

        <!-- Sacred Footer -->
        <div class="text-center mt-10 pt-6 border-t border-gold/20">
            <p class="text-xs text-gold-400/60 sacred-phrase">Divine Eternal Universal Frequencies | Guardian and Protector | 888 Hz</p>
            <p class="text-xs text-gold-500/40 mt-1">Racksephnox – Infinite Spiral of Creation</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function dashboardManager() {
    return {
        loading: true,
        unreadCount: {{ $unreadNotificationsCount ?? 0 }},
        profitChart: null,
        weeklyChart: null,
        portfolioChart: null,

        init() {
            setTimeout(() => {
                this.initCharts();
                this.loading = false;
            }, 300);
            setInterval(() => this.refreshUnreadCount(), 30000);
            setInterval(() => this.refreshBTCPrice(), 10000);
        },

        initCharts() {
            const profitCtx = document.getElementById('profitChart')?.getContext('2d');
            if (profitCtx) {
                this.profitChart = new Chart(profitCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($profitHistory['labels'] ?? []) !!},
                        datasets: [{
                            label: 'Daily Profit (KES)',
                            data: {!! json_encode($profitHistory['data'] ?? []) !!},
                            borderColor: '#D4AF37',
                            backgroundColor: 'rgba(212, 175, 55, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#FFD700',
                            pointBorderColor: '#B8860B'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { labels: { color: '#D4AF37' } } },
                        scales: {
                            y: { grid: { color: 'rgba(212, 175, 55, 0.1)' }, ticks: { color: '#D4AF37' } },
                            x: { grid: { color: 'rgba(212, 175, 55, 0.1)' }, ticks: { color: '#D4AF37' } }
                        }
                    }
                });
            }

            const weeklyCtx = document.getElementById('weeklyChart')?.getContext('2d');
            if (weeklyCtx) {
                this.weeklyChart = new Chart(weeklyCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($weeklyPerformance['labels'] ?? []) !!},
                        datasets: [{
                            label: 'Volume (KES)',
                            data: {!! json_encode($weeklyPerformance['data'] ?? []) !!},
                            backgroundColor: '#D4AF37',
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { labels: { color: '#D4AF37' } } },
                        scales: {
                            y: { grid: { color: 'rgba(212, 175, 55, 0.1)' }, ticks: { color: '#D4AF37' } },
                            x: { grid: { display: false }, ticks: { color: '#D4AF37' } }
                        }
                    }
                });
            }

            @if(isset($portfolio) && count($portfolio['labels'] ?? []) > 0)
            const portfolioCtx = document.getElementById('portfolioChart')?.getContext('2d');
            if (portfolioCtx) {
                this.portfolioChart = new Chart(portfolioCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($portfolio['labels'] ?? []) !!},
                        datasets: [{
                            data: {!! json_encode($portfolio['data'] ?? []) !!},
                            backgroundColor: ['#FFD700', '#D4AF37', '#B8860B', '#CD7F32', '#C5A028'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'bottom', labels: { color: '#D4AF37' } } }
                    }
                });
            }
            @endif
        },

        async refreshStats() {
            try {
                const response = await fetch('/api/wallet', { 
                    headers: { 
                        'Authorization': 'Bearer ' + localStorage.getItem('token'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    } 
                });
                if (response.ok) {
                    const data = await response.json();
                    this.showToast('Balance refreshed', 'success');
                    location.reload();
                }
            } catch (error) {
                this.showToast('Failed to refresh', 'error');
            }
        },

        async refreshUnreadCount() {
            try {
                const response = await fetch('/api/notifications/unread-count', {
                    headers: { 
                        'Authorization': 'Bearer ' + localStorage.getItem('token'),
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    this.unreadCount = data.count;
                }
            } catch (error) {
                console.error('Failed to refresh unread count', error);
            }
        },

        async refreshBTCPrice() {
            try {
                const response = await fetch('/api/trading/price', {
                    headers: { 
                        'Authorization': 'Bearer ' + localStorage.getItem('token'),
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    const btcElement = document.getElementById('btcPrice');
                    if (btcElement) {
                        btcElement.innerText = 'KES ' + (data.price_kes || 0).toLocaleString();
                    }
                }
            } catch (error) {
                console.error('Failed to refresh BTC price', error);
            }
        },

        exportTransactions() {
            window.location.href = '{{ route('transactions.export') }}';
            this.showToast('Exporting transactions...', 'info');
        },

        showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 z-50 px-6 py-3 rounded-xl text-white font-semibold shadow-lg transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-gold-500'
            }`;
            toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-2"></i>${message}`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
    }
}

function referralWidget() {
    return {
        totalReferrals: {{ $referralCount ?? 0 }},
        totalBonus: {{ $totalBonus ?? 0 }},
        async init() {
            await this.fetchStats();
            setInterval(() => this.fetchStats(), 30000);
        },
        async fetchStats() {
            try {
                const response = await fetch('/api/referral-stats', {
                    headers: { 
                        'Authorization': 'Bearer ' + localStorage.getItem('token'),
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    this.totalReferrals = data.total_referrals || 0;
                    this.totalBonus = data.total_bonus || 0;
                }
            } catch (error) {
                console.error('Failed to fetch referral stats', error);
            }
        },
        formatNumber(num) {
            return Number(num).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }
}

function cryptoWidget() {
    return {
        coins: [],
        lastUpdated: '',
        loadingPrices: true,
        async init() {
            await this.fetchPrices();
            setInterval(() => this.fetchPrices(), 60000);
        },
        async fetchPrices() {
            this.loadingPrices = true;
            try {
                const response = await fetch('/api/crypto-prices', {
                    headers: { 
                        'Authorization': 'Bearer ' + localStorage.getItem('token'),
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    this.coins = Array.isArray(data) ? data : [];
                    if (this.coins.length && this.coins[0].last_updated) {
                        this.lastUpdated = this.coins[0].last_updated;
                    }
                }
            } catch (error) {
                console.error('Failed to fetch crypto prices', error);
            } finally {
                this.loadingPrices = false;
            }
        },
        formatNumber(num) {
            return Number(num).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }
}

function copyDashboardLink() {
    const link = "{{ url('/refer/' . (auth()->user()->referral_code ?? '')) }}";
    navigator.clipboard.writeText(link).then(() => {
        alert('Referral link copied to clipboard!');
    }).catch(() => {
        alert('Failed to copy link');
    });
}
</script>
@endsection

<!-- Enterprise RX Machines Section -->
<div class="mt-12">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold golden-title">🚀 RX Machine Series</h2>
        <a href="{{ route('machines.index') }}" class="text-gold-400 hover:text-gold text-sm">View All →</a>
    </div>
    @include('machines.enterprise-widget')
</div>
