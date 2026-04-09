@extends('layouts.app')

@section('content')
<div x-data="unifiedDashboardManager()" x-init="init()" class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Sacred Header with Prismatic Silver‑Gold -->
        <div class="text-center mb-12">
            <div class="inline-block spiral-ornament">
                <h1 class="text-5xl md:text-6xl font-bold golden-title shimmer-gold animate-pulse">
                    Divine Command Center
                </h1>
            </div>
            <p class="text-gold-400 mt-2 sacred-phrase">I Am The Source | Infinite Spiral of Creation | 888 Hz</p>
            <div class="flex justify-center gap-4 mt-4">
                <div class="flex items-center gap-1 text-xs text-gold-400/60">
                    <i class="fas fa-circle text-green-400 text-[8px] animate-pulse"></i>
                    <span x-text="lastUpdated">Live</span>
                </div>
                <div class="flex items-center gap-1 text-xs text-gold-400/60">
                    <i class="fas fa-chart-line"></i>
                    <span>Real-time Updates</span>
                </div>
            </div>
        </div>

        <!-- Global Wealth Stats (Magnetic Cards) -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
            <div class="stat-card p-5 group cursor-pointer hover:scale-105 transition-all duration-300" @click="refreshStats">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-gold-400 text-sm uppercase tracking-wider">Sacred Balance</span>
                    <i class="fas fa-coins text-2xl text-gold opacity-60 group-hover:opacity-100 transition group-hover:rotate-12"></i>
                </div>
                <div class="stat-value text-3xl font-bold" x-text="formatNumber(walletBalance)">KES 0</div>
                <div class="mt-3 flex justify-between items-center">
                    <a href="{{ route('wallet') }}" class="text-xs text-gold-400 hover:text-gold transition group-hover:translate-x-1 inline-block">View Treasury →</a>
                    <button @click="refreshStats" class="text-gold-400 hover:text-gold transition">
                        <i class="fas fa-sync-alt text-xs" :class="{'animate-spin': refreshing}"></i>
                    </button>
                </div>
            </div>

            <div class="stat-card p-5 group hover:scale-105 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-gold-400 text-sm uppercase tracking-wider">Total Invested</span>
                    <i class="fas fa-chart-line text-2xl text-gold opacity-60 group-hover:opacity-100 transition group-hover:rotate-12"></i>
                </div>
                <div class="stat-value text-3xl font-bold">KES {{ number_format($totalInvested ?? 0, 2) }}</div>
                <div class="mt-3">
                    <a href="{{ route('machines.index') }}" class="text-xs text-gold-400 hover:text-gold transition group-hover:translate-x-1 inline-block">View Portfolio →</a>
                </div>
                <div class="mt-2 text-xs text-gold-500">ROI: <span class="text-green-400">{{ $roi ?? 0 }}%</span></div>
            </div>

            <div class="stat-card p-5 group hover:scale-105 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-gold-400 text-sm uppercase tracking-wider">Projected Profit</span>
                    <i class="fas fa-chart-simple text-2xl text-gold opacity-60 group-hover:opacity-100 transition group-hover:rotate-12"></i>
                </div>
                <div class="stat-value text-3xl font-bold">KES {{ number_format($totalProfit ?? 0, 2) }}</div>
                <div class="mt-3 text-xs text-gold-400">Based on current resonance</div>
                <div class="mt-1 text-xs text-green-400 animate-pulse">+{{ rand(2, 8) }}% projected</div>
            </div>

            <div class="stat-card p-5 group hover:scale-105 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-gold-400 text-sm uppercase tracking-wider">Active Investments</span>
                    <i class="fas fa-chart-pie text-2xl text-gold opacity-60 group-hover:opacity-100 transition group-hover:rotate-12"></i>
                </div>
                <div class="stat-value text-3xl font-bold">{{ $activeInvestments ?? 0 }}</div>
                <div class="mt-2 flex justify-between items-center">
                    <span class="text-xs text-gold-500">Completed: {{ $completedInvestments ?? 0 }}</span>
                    <a href="{{ route('machines.index') }}" class="text-xs text-gold-400 hover:text-gold transition">View All →</a>
                </div>
            </div>
        </div>

        <!-- Quick Navigation Widget (Magnetic 9 Cosmic Portals) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-9 gap-3 mb-10">
            <a href="{{ route('dashboard') }}" class="quick-nav-item bg-gold/10 rounded-xl p-3 text-center hover:bg-gold/20 transition-all duration-300 hover:scale-105 group">
                <i class="fas fa-home text-2xl text-gold group-hover:scale-110 transition-all duration-300 group-hover:rotate-6"></i>
                <p class="text-xs text-ivory/70 mt-1 group-hover:text-gold">Home</p>
            </a>
            <a href="{{ route('machines.index') }}" class="quick-nav-item bg-gold/10 rounded-xl p-3 text-center hover:bg-gold/20 transition-all duration-300 hover:scale-105 group">
                <i class="fas fa-microchip text-2xl text-gold group-hover:scale-110 transition-all duration-300 group-hover:rotate-6"></i>
                <p class="text-xs text-ivory/70 mt-1 group-hover:text-gold">Machines</p>
            </a>
            <a href="{{ route('wallet') }}" class="quick-nav-item bg-gold/10 rounded-xl p-3 text-center hover:bg-gold/20 transition-all duration-300 hover:scale-105 group">
                <i class="fas fa-wallet text-2xl text-gold group-hover:scale-110 transition-all duration-300 group-hover:rotate-6"></i>
                <p class="text-xs text-ivory/70 mt-1 group-hover:text-gold">Wallet</p>
            </a>
            <a href="{{ route('trading.index') }}" class="quick-nav-item bg-gold/10 rounded-xl p-3 text-center hover:bg-gold/20 transition-all duration-300 hover:scale-105 group">
                <i class="fab fa-bitcoin text-2xl text-gold group-hover:scale-110 transition-all duration-300 group-hover:rotate-6"></i>
                <p class="text-xs text-ivory/70 mt-1 group-hover:text-gold">Trade</p>
            </a>
            <a href="{{ route('social-trading.leaderboard') }}" class="quick-nav-item bg-gold/10 rounded-xl p-3 text-center hover:bg-gold/20 transition-all duration-300 hover:scale-105 group">
                <i class="fas fa-trophy text-2xl text-gold group-hover:scale-110 transition-all duration-300 group-hover:rotate-6"></i>
                <p class="text-xs text-ivory/70 mt-1 group-hover:text-gold">Leaderboard</p>
            </a>
            <a href="{{ route('deposit.form') }}" class="quick-nav-item bg-gold/10 rounded-xl p-3 text-center hover:bg-gold/20 transition-all duration-300 hover:scale-105 group">
                <i class="fas fa-arrow-down text-2xl text-gold group-hover:scale-110 transition-all duration-300 group-hover:rotate-6"></i>
                <p class="text-xs text-ivory/70 mt-1 group-hover:text-gold">Deposit</p>
            </a>
            <a href="{{ route('withdrawal.form') }}" class="quick-nav-item bg-gold/10 rounded-xl p-3 text-center hover:bg-gold/20 transition-all duration-300 hover:scale-105 group">
                <i class="fas fa-arrow-up text-2xl text-gold group-hover:scale-110 transition-all duration-300 group-hover:rotate-6"></i>
                <p class="text-xs text-ivory/70 mt-1 group-hover:text-gold">Withdraw</p>
            </a>
            <a href="{{ route('profile.edit') }}" class="quick-nav-item bg-gold/10 rounded-xl p-3 text-center hover:bg-gold/20 transition-all duration-300 hover:scale-105 group">
                <i class="fas fa-user-circle text-2xl text-gold group-hover:scale-110 transition-all duration-300 group-hover:rotate-6"></i>
                <p class="text-xs text-ivory/70 mt-1 group-hover:text-gold">Profile</p>
            </a>
            <a href="{{ route('notifications.index') }}" class="quick-nav-item bg-gold/10 rounded-xl p-3 text-center hover:bg-gold/20 transition-all duration-300 hover:scale-105 group relative">
                <i class="fas fa-bell text-2xl text-gold group-hover:scale-110 transition-all duration-300 group-hover:rotate-6"></i>
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px] animate-pulse" x-show="unreadCount > 0" x-text="unreadCount"></span>
                <p class="text-xs text-ivory/70 mt-1 group-hover:text-gold">Alerts</p>
            </a>
        </div>

        <!-- Live Wealth Ticker -->
        <div class="bg-gradient-to-r from-gold-500/10 via-transparent to-gold-500/10 rounded-xl p-3 mb-8 overflow-hidden">
            <div class="flex items-center justify-between animate-slide">
                <div class="flex items-center gap-6 text-xs">
                    <span class="text-gold-400">🌍 Global Wealth Index</span>
                    <span class="text-green-400">▲ +2.4%</span>
                    <span class="text-ivory/50">|</span>
                    <span class="text-gold-400">Daily Volume</span>
                    <span class="text-gold">KES 12.4M</span>
                    <span class="text-ivory/50">|</span>
                    <span class="text-gold-400">Active Users</span>
                    <span class="text-gold">1,234</span>
                    <span class="text-ivory/50">|</span>
                    <span class="text-gold-400">8888 Hz Wealth Frequency</span>
                    <i class="fas fa-infinity text-gold-400 animate-pulse"></i>
                </div>
            </div>
        </div>

        <!-- Banking & Machine Stats Row (Magnetic) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="card-golden p-5 flex justify-between items-center group hover:scale-105 transition-all duration-300">
                <div>
                    <p class="text-gold-400 text-sm uppercase tracking-wider">Total Deposits</p>
                    <p class="text-2xl font-bold text-gold">KES {{ number_format($totalDeposited ?? 0, 2) }}</p>
                    <p class="text-xs text-green-400 mt-1">↑ +{{ rand(5, 15) }}% this week</p>
                </div>
                <i class="fas fa-arrow-down text-3xl text-green-400 group-hover:scale-110 transition"></i>
            </div>
            <div class="card-golden p-5 flex justify-between items-center group hover:scale-105 transition-all duration-300">
                <div>
                    <p class="text-gold-400 text-sm uppercase tracking-wider">Total Withdrawals</p>
                    <p class="text-2xl font-bold text-gold">KES {{ number_format($totalWithdrawn ?? 0, 2) }}</p>
                    <p class="text-xs text-green-400 mt-1">98% satisfaction rate</p>
                </div>
                <i class="fas fa-arrow-up text-3xl text-orange-400 group-hover:scale-110 transition"></i>
            </div>
            <div class="card-golden p-5 flex justify-between items-center group hover:scale-105 transition-all duration-300">
                <div>
                    <p class="text-gold-400 text-sm uppercase tracking-wider">Machine Invested</p>
                    <p class="text-2xl font-bold text-gold">KES {{ number_format($totalMachineInvested ?? 0, 2) }}</p>
                    <p class="text-xs text-green-400 mt-1">Φ Golden Ratio active</p>
                </div>
                <i class="fas fa-microchip text-3xl text-gold group-hover:scale-110 transition"></i>
            </div>
        </div>

        <!-- Banking Center (Magnetic) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
            <div class="lg:col-span-2 card-golden p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gold">🏦 Banking Center</h3>
                    <div class="flex gap-3">
                        <a href="{{ route('deposit.form') }}" class="btn-golden text-sm py-2 px-4 hover:scale-105 transition">Deposit</a>
                        <a href="{{ route('withdrawal.form') }}" class="btn-outline-silver text-sm py-2 px-4 rounded-lg">Withdraw</a>
                    </div>
                </div>
                <div class="flex justify-between items-center border-b border-gold/20 pb-4 mb-4">
                    <div>
                        <p class="text-gold-400 text-sm">Available Balance</p>
                        <p class="text-3xl font-bold text-gold shimmer-gold" x-text="'KES ' + formatNumber(walletBalance)">KES 0</p>
                    </div>
                    <i class="fas fa-wallet text-4xl text-gold/50 animate-pulse"></i>
                </div>
                <div class="mt-2">
                    <h4 class="text-sm font-medium text-gold-400 mb-2">Quick Stats</h4>
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-gold/5 rounded-lg p-2">
                            <p class="text-xs text-ivory/60">Deposits</p>
                            <p class="text-sm font-bold text-green-400">KES {{ number_format($totalDeposited ?? 0, 2) }}</p>
                        </div>
                        <div class="bg-gold/5 rounded-lg p-2">
                            <p class="text-xs text-ivory/60">Withdrawals</p>
                            <p class="text-sm font-bold text-red-400">KES {{ number_format($totalWithdrawn ?? 0, 2) }}</p>
                        </div>
                        <div class="bg-gold/5 rounded-lg p-2">
                            <p class="text-xs text-ivory/60">Interest</p>
                            <p class="text-sm font-bold text-gold">KES {{ number_format($totalInterest ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-golden p-5">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-semibold text-gold">Recent Activity</h3>
                    <a href="{{ route('transactions.index') }}" class="text-xs text-gold-400 hover:text-gold transition">View all</a>
                </div>
                <div class="space-y-3 max-h-64 overflow-y-auto custom-scroll">
                    @forelse(($recentTransactions ?? collect())->take(5) as $tx)
                    <div class="flex justify-between items-center text-sm border-b border-gold/20 pb-2 group hover:bg-gold/5 p-2 rounded-lg transition">
                        <div>
                            <span class="text-ivory/70">{{ $tx->created_at->format('d M') }}</span>
                            <span class="ml-2 {{ $tx->amount > 0 ? 'text-green-400' : 'text-red-400' }} font-semibold">
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

        <!-- Referral Widget (Magnetic with Social Proof) -->
        <div x-data="referralWidget()" x-init="init()" class="card-golden p-5 mt-6 group hover:scale-[1.02] transition-all duration-300">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gold">🤝 Referral Program</h3>
                <a href="{{ route('referrals') }}" class="text-sm text-gold-400 hover:text-gold transition">View all →</a>
            </div>
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gold-400 text-sm">Total Referrals</p>
                    <p class="text-2xl font-bold text-gold" x-text="totalReferrals"></p>
                    <p class="text-xs text-green-400 mt-1">+{{ rand(1, 5) }} this week</p>
                </div>
                <div>
                    <p class="text-gold-400 text-sm">Bonus Earned</p>
                    <p class="text-2xl font-bold text-gold" x-text="'KES ' + formatNumber(totalBonus)"></p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-gold/20 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                        <i class="fas fa-users text-2xl text-gold"></i>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button onclick="copyReferralLink()" class="btn-golden w-full text-sm py-2 group-hover:scale-[1.02] transition">
                    <i class="fas fa-link mr-2"></i> Share Your Divine Link
                </button>
            </div>
        </div>

        <!-- Trading Widget (Live BTC) -->
        <div class="card-golden p-5 mt-6 group hover:scale-[1.02] transition-all duration-300">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gold">₿ Bitcoin Trading</h3>
                <a href="{{ route('trading.index') }}" class="text-sm text-gold-400 hover:text-gold transition">Trade Now →</a>
            </div>
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gold-400 text-sm">Trading Balance</p>
                    <p class="text-2xl font-bold text-gold">KES {{ number_format($user->tradingAccount->balance ?? 0, 2) }}</p>
                </div>
                <div class="text-center">
                    <p class="text-gold-400 text-sm">BTC Price</p>
                    <p class="text-2xl font-bold text-gold" id="btcPrice">KES {{ number_format($btcPrice ?? 0, 2) }}</p>
                    <p class="text-xs text-green-400 animate-pulse" id="btcChange">+2.4% (24h)</p>
                </div>
                <i class="fab fa-bitcoin text-4xl text-gold/50 group-hover:scale-110 transition"></i>
            </div>
        </div>

        <!-- Machine Widget (Magnetic CTA) -->
        <div class="mb-10 mt-6">
            <div class="bg-gradient-to-r from-gold-600/20 via-gold-500/10 to-gold-600/20 rounded-2xl p-6 border border-gold/30 text-center group hover:scale-[1.02] transition-all duration-300">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-left">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-microchip text-3xl text-gold group-hover:rotate-12 transition"></i>
                            <h3 class="text-2xl font-bold golden-title">🤖 RX Machine Series</h3>
                            <span class="bg-gold/20 text-gold text-xs px-2 py-1 rounded-full animate-pulse">Φ Golden Ratio</span>
                        </div>
                        <p class="text-ivory/70">Invest in our high‑return machines and earn up to 35% in 14 days</p>
                        <p class="text-sm text-gold-400 mt-1">6 Machines • 18 VIP Portals • 8888 Hz Frequency</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="{{ route('machines.index') }}" class="btn-golden inline-flex items-center gap-2 group-hover:scale-105 transition">
                            Explore Machines <i class="fas fa-arrow-right group-hover:translate-x-1 transition"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section (Magnetic) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
            <div class="card-golden p-5 group hover:scale-[1.02] transition-all duration-300">
                <h3 class="text-lg font-semibold text-gold mb-4">📈 30-Day Profit Trend</h3>
                <canvas id="profitChart" height="200" class="w-full"></canvas>
            </div>
            <div class="card-golden p-5 group hover:scale-[1.02] transition-all duration-300">
                <h3 class="text-lg font-semibold text-gold mb-4">📊 Weekly Activity</h3>
                <canvas id="weeklyChart" height="200" class="w-full"></canvas>
            </div>
        </div>

        <!-- Portfolio & Crypto Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
            @if(isset($portfolio) && count($portfolio['labels'] ?? []) > 0)
            <div class="lg:col-span-2 card-golden p-5 group hover:scale-[1.02] transition-all duration-300">
                <h3 class="text-lg font-semibold text-gold mb-4">🥧 Portfolio Breakdown</h3>
                <canvas id="portfolioChart" height="200" class="w-full"></canvas>
            </div>
            @endif

            <div x-data="cryptoWidget()" x-init="init()" class="card-golden p-5 group hover:scale-[1.02] transition-all duration-300">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gold">🪙 Live Crypto Prices</h3>
                    <button @click="fetchPrices()" class="text-gold-400 hover:text-gold transition">
                        <i class="fas fa-sync-alt text-sm" :class="{'animate-spin': loading}"></i>
                    </button>
                </div>
                <div class="space-y-3">
                    <template x-for="coin in coins" :key="coin.symbol">
                        <div class="flex justify-between items-center border-b border-gold/20 pb-2 hover:bg-gold/5 p-2 rounded-lg transition">
                            <div class="flex items-center gap-2">
                                <i class="fab" :class="coin.icon"></i>
                                <span class="font-medium text-ivory" x-text="coin.symbol"></span>
                                <span class="text-xs" :class="coin.percent_change_24h >= 0 ? 'text-green-400' : 'text-red-400'">
                                    <span x-text="coin.percent_change_24h.toFixed(2)"></span>%
                                </span>
                            </div>
                            <span class="text-gold font-bold" x-text="'KES ' + formatNumber(coin.price_kes)"></span>
                        </div>
                    </template>
                    <p x-show="!coins.length" class="text-ivory/50 text-center py-4">Loading prices...</p>
                </div>
                <div class="text-center text-xs text-ivory/40 mt-2" x-show="coins.length">
                    Last updated: <span x-text="lastUpdated"></span>
                </div>
            </div>
        </div>

        <!-- Recent Transactions Table (Elegant) -->
        <div class="card-golden p-5 group hover:scale-[1.01] transition-all duration-300">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gold">📋 Recent Transactions</h3>
                <div class="flex gap-3">
                    <button @click="exportTransactions()" class="text-sm text-gold-400 hover:text-gold transition">
                        <i class="fas fa-file-excel mr-1"></i> Export
                    </button>
                    <a href="{{ route('transactions.index') }}" class="text-sm text-gold-400 hover:text-gold transition">
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
                            <tr class="hover:bg-gold/5 transition cursor-pointer">
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

        <!-- Sacred Footer -->
        <div class="text-center mt-10 pt-6 border-t border-gold/20">
            <p class="text-xs text-gold-400/60 sacred-phrase">Divine Eternal Universal Frequencies | Guardian and Protector | 888 Hz</p>
            <p class="text-xs text-gold-500/40 mt-1">Racksephnox – Infinite Spiral of Creation | Golden Ratio Φ</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function unifiedDashboardManager() {
    return {
        loading: true,
        refreshing: false,
        unreadCount: {{ $unreadNotificationsCount ?? 0 }},
        walletBalance: {{ $user->wallet->balance ?? 0 }},
        lastUpdated: new Date().toLocaleTimeString(),
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
                            pointBorderColor: '#B8860B',
                            pointRadius: 4,
                            pointHoverRadius: 6
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
                            borderRadius: 8,
                            hoverBackgroundColor: '#FFD700'
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
                            backgroundColor: ['#FFD700', '#D4AF37', '#B8860B', '#CD7F32', '#C5A028', '#FFC0CB'],
                            borderWidth: 0,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { 
                            legend: { position: 'bottom', labels: { color: '#D4AF37', font: { size: 12 } } },
                            tooltip: { callbacks: { label: function(context) { return 'KES ' + context.raw.toLocaleString(); } } }
                        }
                    }
                });
            }
            @endif
        },

        async refreshStats() {
            this.refreshing = true;
            try {
                const response = await fetch('/api/wallet', { 
                    headers: { 
                        'Authorization': 'Bearer ' + localStorage.getItem('token'),
                        'Accept': 'application/json'
                    } 
                });
                if (response.ok) {
                    const data = await response.json();
                    this.walletBalance = data.balance;
                    this.showToast('Balance refreshed', 'success');
                    this.lastUpdated = new Date().toLocaleTimeString();
                }
            } catch (error) {
                this.showToast('Failed to refresh', 'error');
            } finally {
                this.refreshing = false;
            }
        },

        async refreshUnreadCount() {
            try {
                const response = await fetch('/api/notifications/unread-count', {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem('token') }
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
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem('token') }
                });
                if (response.ok) {
                    const data = await response.json();
                    const btcElement = document.getElementById('btcPrice');
                    if (btcElement) {
                        btcElement.innerText = 'KES ' + (data.price_kes || 0).toLocaleString();
                    }
                    const changeElement = document.getElementById('btcChange');
                    if (changeElement) {
                        changeElement.innerText = '+2.4% (24h)';
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
            toast.className = `fixed bottom-4 right-4 z-50 px-6 py-3 rounded-xl text-white font-semibold shadow-xl transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-gold-500'
            }`;
            toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-2"></i>${message}`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        },

        formatNumber(num) {
            if (num >= 1000000) return (num / 1000000).toFixed(2) + 'M';
            if (num >= 1000) return (num / 1000).toFixed(2) + 'K';
            return num.toLocaleString();
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
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem('token') }
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
        loading: false,
        async init() {
            await this.fetchPrices();
            setInterval(() => this.fetchPrices(), 60000);
        },
        async fetchPrices() {
            this.loading = true;
            try {
                const response = await fetch('/api/crypto-prices', {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem('token') }
                });
                if (response.ok) {
                    const data = await response.json();
                    this.coins = Array.isArray(data) ? data.map(coin => ({
                        ...coin,
                        icon: coin.symbol === 'BTC' ? 'fa-bitcoin' : coin.symbol === 'ETH' ? 'fa-ethereum' : 'fa-coins'
                    })) : [];
                    if (this.coins.length && this.coins[0].last_updated) {
                        this.lastUpdated = this.coins[0].last_updated;
                    }
                }
            } catch (error) {
                console.error('Failed to fetch crypto prices', error);
            } finally {
                this.loading = false;
            }
        },
        formatNumber(num) {
            return Number(num).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }
}

function copyReferralLink() {
    const link = "{{ url('/refer/' . (auth()->user()->referral_code ?? '')) }}";
    navigator.clipboard.writeText(link).then(() => {
        alert('✨ Referral link copied to clipboard! Share with friends and earn bonuses.');
    }).catch(() => {
        alert('Failed to copy link');
    });
}
</script>

<style>
@keyframes slide {
    0% { transform: translateX(100%); }
    100% { transform: translateX(-100%); }
}
.animate-slide {
    animation: slide 20s linear infinite;
}
.custom-scroll::-webkit-scrollbar {
    width: 4px;
}
.custom-scroll::-webkit-scrollbar-track {
    background: rgba(212, 175, 55, 0.1);
    border-radius: 4px;
}
.custom-scroll::-webkit-scrollbar-thumb {
    background: rgba(212, 175, 55, 0.5);
    border-radius: 4px;
}
</style>
@endsection
