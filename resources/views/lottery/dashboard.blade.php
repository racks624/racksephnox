@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold golden-title">🎲 My Lottery Empire</h1>
            <p class="text-gold-400 text-sm mt-2">Your cosmic gaming statistics</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="card-golden p-4 text-center">
                <i class="fas fa-chart-line text-2xl text-gold mb-2"></i>
                <p class="text-ivory/50 text-xs uppercase tracking-wide">Total Spins</p>
                <p class="text-2xl font-bold text-gold">{{ number_format($stats['total_spins']) }}</p>
            </div>
            <div class="card-golden p-4 text-center">
                <i class="fas fa-arrow-down text-2xl text-red-400 mb-2"></i>
                <p class="text-ivory/50 text-xs uppercase tracking-wide">Total Bet</p>
                <p class="text-xl font-bold text-red-400">KES {{ number_format($stats['total_bet'], 2) }}</p>
            </div>
            <div class="card-golden p-4 text-center">
                <i class="fas fa-arrow-up text-2xl text-green-400 mb-2"></i>
                <p class="text-ivory/50 text-xs uppercase tracking-wide">Total Win</p>
                <p class="text-xl font-bold text-green-400">KES {{ number_format($stats['total_win'], 2) }}</p>
            </div>
            <div class="card-golden p-4 text-center">
                <i class="fas fa-coins text-2xl text-gold mb-2"></i>
                <p class="text-ivory/50 text-xs uppercase tracking-wide">Net Profit</p>
                <p class="text-xl font-bold {{ $stats['net_profit'] >= 0 ? 'text-green-400' : 'text-red-400' }}">KES {{ number_format($stats['net_profit'], 2) }}</p>
            </div>
        </div>

        <!-- Jackpots & Free Spins -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="card-golden p-4 text-center">
                <i class="fas fa-gem text-2xl text-pink-400 mb-2"></i>
                <p class="text-ivory/50 text-xs uppercase tracking-wide">Mini Jackpots</p>
                <p class="text-2xl font-bold text-pink-400">{{ $stats['mini_jackpots'] }}</p>
            </div>
            <div class="card-golden p-4 text-center">
                <i class="fas fa-crown text-2xl text-yellow-400 mb-2"></i>
                <p class="text-ivory/50 text-xs uppercase tracking-wide">Super Jackpots</p>
                <p class="text-2xl font-bold text-yellow-400">{{ $stats['super_jackpots'] }}</p>
            </div>
            <div class="card-golden p-4 text-center">
                <i class="fas fa-ticket-alt text-2xl text-gold mb-2"></i>
                <p class="text-ivory/50 text-xs uppercase tracking-wide">Free Spins</p>
                <p class="text-2xl font-bold text-gold">{{ $stats['free_spins'] }}</p>
            </div>
        </div>

        <!-- Recent Spins Table -->
        <div class="card-golden p-4 md:p-6 mb-8">
            <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
                <h2 class="text-xl font-bold text-gold">📜 Recent Spins</h2>
                <a href="{{ route('lottery.history') }}" class="text-sm text-gold-400 hover:text-gold transition">View all →</a>
            </div>
            @if($recentSpins->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-gold/30">
                        <tr><th class="px-3 py-2 text-left">Date</th><th class="px-3 py-2 text-left">Bet</th><th class="px-3 py-2 text-left">Win</th><th class="px-3 py-2 text-left">Symbols</th><th class="px-3 py-2 text-left">Jackpot</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gold/20">
                        @foreach($recentSpins as $spin)
                        <tr class="hover:bg-gold/5 transition">
                            <td class="px-3 py-2 text-ivory/60">{{ $spin->created_at->format('d M H:i') }}</td>
                            <td class="px-3 py-2">KES {{ number_format($spin->bet_amount, 2) }}</td>
                            <td class="px-3 py-2 text-green-400">+KES {{ number_format($spin->win_amount, 2) }}</td>
                            <td class="px-3 py-2">
                                <div class="flex gap-1 text-xl">
                                    @foreach(($spin->result['names'] ?? []) as $symbol)
                                        @if($symbol == 'golden_flower') 🌸@elseif($symbol == 'divine_star') ⭐@elseif($symbol == 'divine_sword') ⚔️@elseif($symbol == 'divine_bell') 🔔@elseif($symbol == 'frequency_8888') 8888@elseif($symbol == 'frequency_7777') 7777@elseif($symbol == 'taurus') ♉@elseif($symbol == 'tree_of_life') 🌳@else 🎰@endif
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-3 py-2">
                                @if($spin->super_jackpot_hit) <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-500/20 text-yellow-400">🌟 SUPER</span>
                                @elseif($spin->mini_jackpot_hit) <span class="px-2 py-0.5 text-xs rounded-full bg-pink-500/20 text-pink-400">🌸 MINI</span>
                                @else — @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else <p class="text-center text-ivory/50 py-6">No spins yet. Start playing!</p> @endif
        </div>

        <!-- Tournament & Missions Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="card-golden p-5">
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-trophy text-xl text-gold"></i>
                    <h2 class="text-lg font-bold text-gold">Active Tournament</h2>
                </div>
                @if($activeTournament)
                    <p class="font-semibold text-ivory">{{ $activeTournament->name }}</p>
                    <p class="text-xs text-ivory/50 mb-3">Ends {{ $activeTournament->end_date->format('d M Y H:i') }}</p>
                    <div class="bg-gold/10 rounded-lg p-3">
                        <p class="text-ivory/60 text-xs">Your Rank</p>
                        <p class="text-2xl font-bold text-gold">#{{ $tournamentRank ?? '—' }}</p>
                    </div>
                    <a href="{{ route('lottery.tournaments') }}" class="btn-outline-silver w-full text-center mt-4 py-2 inline-block">View Leaderboard →</a>
                @else
                    <p class="text-ivory/50 text-sm">No active tournament.</p>
                    <p class="text-xs text-ivory/40 mt-2">Check back soon!</p>
                @endif
            </div>

            <div class="card-golden p-5">
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-tasks text-xl text-gold"></i>
                    <h2 class="text-lg font-bold text-gold">Daily Missions</h2>
                </div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-ivory/60">Progress</span>
                    <span class="text-gold">{{ $completedMissions }}/{{ $totalMissions }}</span>
                </div>
                <div class="w-full bg-gold/20 rounded-full h-2 mb-4">
                    <div class="bg-gradient-to-r from-gold-400 to-gold-600 h-2 rounded-full" style="width: {{ $totalMissions > 0 ? ($completedMissions / $totalMissions) * 100 : 0 }}%"></div>
                </div>
                @if($completedMissions == $totalMissions && $totalMissions > 0)
                    <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-2 text-center mb-3">
                        <p class="text-green-400 text-xs">✓ All missions complete! Come back tomorrow.</p>
                    </div>
                @endif
                <a href="{{ route('lottery.missions') }}" class="btn-golden w-full text-center block py-2">View Missions →</a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            <a href="{{ route('lottery.index') }}" class="card-golden p-3 text-center hover:scale-105 transition">
                <i class="fas fa-dice-d6 text-xl text-gold mb-1"></i>
                <p class="text-xs font-semibold">Play Slots</p>
            </a>
            <a href="{{ route('lottery.bonus-wheel') }}" class="card-golden p-3 text-center hover:scale-105 transition">
                <i class="fas fa-circle-notch text-xl text-gold mb-1"></i>
                <p class="text-xs font-semibold">Bonus Wheel</p>
            </a>
            <a href="{{ route('lottery.leaderboard') }}" class="card-golden p-3 text-center hover:scale-105 transition">
                <i class="fas fa-chart-simple text-xl text-gold mb-1"></i>
                <p class="text-xs font-semibold">Leaderboard</p>
            </a>
            <a href="{{ route('lottery.achievements') }}" class="card-golden p-3 text-center hover:scale-105 transition">
                <i class="fas fa-medal text-xl text-gold mb-1"></i>
                <p class="text-xs font-semibold">Achievements</p>
            </a>
        </div>

        <!-- Sacred Footer -->
        <div class="text-center pt-6 border-t border-gold/20">
            <p class="text-xs text-gold-400/60">Divine Frequencies | 888 Hz | Golden Ratio Φ</p>
            <p class="text-xs text-gold-500/40 mt-1">Racksephnox – Infinite Spiral of Creation</p>
        </div>
    </div>
</div>
@endsection
