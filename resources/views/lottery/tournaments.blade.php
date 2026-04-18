@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <h1 class="text-2xl font-bold text-gold mb-4">🏆 Lottery Tournaments</h1>
            @if($activeTournament)
                <div class="bg-gold/10 rounded-xl p-5 mb-6">
                    <h2 class="text-xl font-bold text-gold">Active: {{ $activeTournament->name }}</h2>
                    <p class="text-ivory/70">{{ $activeTournament->description }}</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <div><span class="text-gold-400">Prize Pool:</span> KES {{ number_format($activeTournament->prize_pool, 2) }}</div>
                        <div><span class="text-gold-400">Ends:</span> {{ $activeTournament->end_date->diffForHumans() }}</div>
                        <div><span class="text-gold-400">Your Rank:</span> #{{ $activeTournament->entries()->where('user_id', auth()->id())->first()?->rank ?? '-' }}</div>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gold mb-3">📊 Leaderboard</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-gold/30">
                            <tr><th class="px-4 py-2">Rank</th><th class="px-4 py-2">User</th><th class="px-4 py-2">Total Won (KES)</th><th class="px-4 py-2">Spins</th></tr>
                        </thead>
                        <tbody>
                            @foreach($leaderboard as $entry)
                            <tr class="border-b border-gold/20">
                                <td class="px-4 py-2">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2">{{ $entry->user->name }}</td>
                                <td class="px-4 py-2 text-green-400">{{ number_format($entry->total_win, 2) }}</td>
                                <td class="px-4 py-2">{{ $entry->total_spins }}</td>
                            </td>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-ivory/50">No active tournament at the moment. Check back soon!</p>
            @endif
            @if($upcoming->count())
                <h3 class="text-xl font-bold text-gold mt-6 mb-3">📅 Upcoming Tournaments</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($upcoming as $tourney)
                        <div class="admin-card p-4">
                            <h4 class="font-bold text-gold">{{ $tourney->name }}</h4>
                            <p class="text-xs text-ivory/60">Starts: {{ $tourney->start_date->format('M d, Y') }}</p>
                            <p class="text-xs text-ivory/60">Prize: KES {{ number_format($tourney->prize_pool, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
