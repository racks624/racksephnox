@extends('admin.layouts.app')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gold">🎰 Lottery Management</h1>

    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
        <div class="admin-card p-4 text-center"><p class="text-ivory/60">Total Spins</p><p class="text-2xl font-bold text-gold">{{ number_format($totalBets) }}</p></div>
        <div class="admin-card p-4 text-center"><p class="text-ivory/60">Total Wagered</p><p class="text-2xl font-bold text-gold">KES {{ number_format($totalBets, 2) }}</p></div>
        <div class="admin-card p-4 text-center"><p class="text-ivory/60">Total Payout</p><p class="text-2xl font-bold text-gold">KES {{ number_format($totalWins, 2) }}</p></div>
        <div class="admin-card p-4 text-center"><p class="text-ivory/60">Mini Jackpot Hits</p><p class="text-2xl font-bold text-pink-400">{{ number_format($miniJackpotHits) }}</p></div>
        <div class="admin-card p-4 text-center"><p class="text-ivory/60">Super Jackpot Hits</p><p class="text-2xl font-bold text-gold">{{ number_format($superJackpotHits) }}</p></div>
        <div class="admin-card p-4 text-center"><p class="text-ivory/60">Total Tax Collected</p><p class="text-2xl font-bold text-yellow-400">KES {{ number_format($totalTax, 2) }}</p></div>
    </div>

    <div class="admin-card p-5">
        <h2 class="text-xl font-bold text-gold mb-4">🎲 Active Games</h2>
        @foreach($games as $game)
        <div class="border-b border-gold/20 py-4 flex flex-wrap justify-between items-center">
            <div><p class="font-bold text-ivory">{{ $game->name }}</p><p class="text-sm text-ivory/60">Min: KES {{ number_format($game->min_bet, 2) }} | Max: KES {{ number_format($game->max_bet, 2) }} | Super Jackpot: KES {{ number_format($game->progressive_jackpot, 2) }}</p></div>
            <a href="{{ route('admin.lottery.edit-game', $game) }}" class="btn-golden text-sm py-1 px-3">Edit Game</a>
        </div>
        @endforeach
    </div>

    <div class="admin-card p-5">
        <h2 class="text-xl font-bold text-gold mb-4">🌀 Slot Symbols</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gold/30">
                    <tr class="text-gold-400"><th class="px-4 py-2 text-left">Symbol</th><th class="px-4 py-2 text-left">Display</th><th class="px-4 py-2 text-left">Weight</th><th class="px-4 py-2 text-left">Wild</th><th class="px-4 py-2 text-left">Scatter</th><th class="px-4 py-2 text-left">Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($symbols as $symbol)
                    <tr class="border-b border-gold/20">
                        <td class="px-4 py-2"><i class="fas {{ $symbol->icon }} text-gold mr-2"></i> {{ $symbol->name }}</td>
                        <td class="px-4 py-2">{{ $symbol->display_name }}</td>
                        <td class="px-4 py-2">{{ $symbol->weight }}</td>
                        <td class="px-4 py-2">@if($symbol->is_wild) ✅ @else ❌ @endif</td>
                        <td class="px-4 py-2">@if($symbol->is_scatter) ✅ @else ❌ @endif</td>
                        <td class="px-4 py-2"><a href="{{ route('admin.lottery.edit-symbol', $symbol) }}" class="text-gold-400 hover:text-gold">Edit</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-card p-5">
        <h2 class="text-xl font-bold text-gold mb-4">📜 Recent Spins</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gold/30">
                    <tr class="text-gold-400"><th class="px-4 py-2">User</th><th class="px-4 py-2">Bet</th><th class="px-4 py-2">Win</th><th class="px-4 py-2">Mini</th><th class="px-4 py-2">Super</th><th class="px-4 py-2">Time</th></tr>
                </thead>
                <tbody>
                    @forelse($recentSpins as $spin)
                    <tr class="border-b border-gold/20">
                        <td class="px-4 py-2">{{ $spin->user->name ?? '?' }}</td>
                        <td class="px-4 py-2">KES {{ number_format($spin->bet_amount, 2) }}</td>
                        <td class="px-4 py-2 text-green-400">KES {{ number_format($spin->win_amount, 2) }}</td>
                        <td class="px-4 py-2">@if($spin->mini_jackpot_hit) 🌸 @else — @endif</td>
                        <td class="px-4 py-2">@if($spin->super_jackpot_hit) 🌟 @else — @endif</td>
                        <td class="px-4 py-2 text-ivory/60">{{ $spin->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-ivory/50">No spins yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
