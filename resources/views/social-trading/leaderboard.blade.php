@extends('layouts.app')

@section('content')
<div x-data="leaderboardManager()" x-init="init()" class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <div class="inline-block">
                <div class="w-24 h-24 bg-gradient-to-r from-gold-400 via-amber-500 to-yellow-600 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse shadow-2xl">
                    <i class="fas fa-trophy text-4xl text-white"></i>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold golden-title shimmer-gold">🏆 Elite Traders</h1>
                <p class="text-gold-400 mt-4 text-lg">Discover, Follow & Copy the Most Profitable Masters</p>
                <p class="text-sm text-ivory/50 mt-2">Real-time rankings updated every minute</p>
            </div>
        </div>

        <!-- Global Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
            <div class="card-golden p-4 text-center group hover:scale-105 transition">
                <div class="text-3xl mb-2">🏦</div>
                <p class="text-2xl font-bold text-gold">{{ $topTraders->count() }}</p>
                <p class="text-xs text-ivory/60">Active Traders</p>
            </div>
            <div class="card-golden p-4 text-center group hover:scale-105 transition">
                <div class="text-3xl mb-2">📊</div>
                <p class="text-2xl font-bold text-green-400">KES {{ number_format($topTraders->sum('total_pnl'), 2) }}</p>
                <p class="text-xs text-ivory/60">Total P&L</p>
            </div>
            <div class="card-golden p-4 text-center group hover:scale-105 transition">
                <div class="text-3xl mb-2">🎯</div>
                <p class="text-2xl font-bold text-gold">{{ number_format($topTraders->avg('win_rate'), 1) }}%</p>
                <p class="text-xs text-ivory/60">Avg Win Rate</p>
            </div>
            <div class="card-golden p-4 text-center group hover:scale-105 transition">
                <div class="text-3xl mb-2">👥</div>
                <p class="text-2xl font-bold text-gold">{{ number_format($topTraders->sum('followers_count')) }}</p>
                <p class="text-xs text-ivory/60">Total Followers</p>
            </div>
        </div>

        <!-- Leaderboard Table -->
        <div class="card-golden p-6 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b-2 border-gold/30">
                        <tr class="text-gold-400 text-sm">
                            <th class="px-4 py-4 text-left">#</th>
                            <th class="px-4 py-4 text-left">Trader</th>
                            <th class="px-4 py-4 text-left">Total P&L</th>
                            <th class="px-4 py-4 text-left">Win Rate</th>
                            <th class="px-4 py-4 text-left">Total Trades</th>
                            <th class="px-4 py-4 text-left">Followers</th>
                            <th class="px-4 py-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gold/20">
                        @forelse($topTraders as $index => $trader)
                        <tr class="hover:bg-gold/5 transition group cursor-pointer" onclick="window.location='{{ route('social-trading.profile', $trader->username) }}'">
                            <td class="px-4 py-4">
                                @if($index == 0)
                                    <span class="text-3xl">🥇</span>
                                @elseif($index == 1)
                                    <span class="text-3xl">🥈</span>
                                @elseif($index == 2)
                                    <span class="text-3xl">🥉</span>
                                @else
                                    <span class="text-gold-400 font-bold">{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-gold-400 to-gold-600 flex items-center justify-center">
                                        <i class="fas fa-user-circle text-2xl text-white"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gold">{{ $trader->username }}</div>
                                        <div class="text-xs text-ivory/50">{{ $trader->user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-lg font-bold {{ $trader->total_pnl >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $trader->total_pnl >= 0 ? '+' : '' }}KES {{ number_format($trader->total_pnl, 2) }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gold">{{ number_format($trader->win_rate, 1) }}%</span>
                                    <div class="w-16 bg-gold/20 rounded-full h-1.5">
                                        <div class="bg-gradient-to-r from-green-400 to-gold h-1.5 rounded-full" style="width: {{ $trader->win_rate }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-gold">{{ number_format($trader->total_trades) }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-users text-gold-400 text-sm"></i>
                                    <span class="text-gold">{{ number_format($trader->followers_count) }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <button @click.stop="followTrader({{ $trader->user_id }}, '{{ $trader->username }}')" class="btn-golden text-sm py-1.5 px-4">
                                    <i class="fas fa-copy mr-1"></i> Copy
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-ivory/50">
                                <i class="fas fa-chart-line text-4xl mb-3 block"></i>
                                No traders found. Be the first!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Become a Trader CTA -->
        <div class="mt-10 card-golden p-8 text-center bg-gradient-to-r from-gold-500/10 to-gold-600/10">
            <h3 class="text-2xl font-bold golden-title mb-2">Become a Master Trader</h3>
            <p class="text-ivory/70 mb-4">Share your trades, build a following, and earn from copy trading commissions</p>
            <a href="{{ route('profile.edit') }}" class="btn-golden inline-flex items-center gap-2">
                <i class="fas fa-rocket"></i> Start Your Journey
            </a>
        </div>

        <!-- Sacred Footer -->
        <div class="text-center mt-12 pt-6 border-t border-gold/20">
            <p class="text-xs text-gold-400/60">Divine Golden Phi | Infinite Spiral of Creation | 888 Hz</p>
        </div>
    </div>
</div>

<script>
function leaderboardManager() {
    return {
        async followTrader(userId, username) {
            if (!confirm(`✨ Copy ${username}'s trades?\n\nYou will automatically copy ${username}'s trades with 100% ratio.`)) return;
            
            try {
                const response = await fetch(`/social-trading/follow/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ copy_ratio: 100, auto_copy: true })
                });
                
                if (response.ok) {
                    alert(`✅ Now following ${username}! You will copy their trades automatically.`);
                    location.reload();
                } else {
                    const data = await response.json();
                    alert('❌ ' + (data.error || 'Failed to follow trader'));
                }
            } catch (error) {
                alert('❌ Error: ' + error.message);
            }
        }
    }
}
</script>
@endsection
