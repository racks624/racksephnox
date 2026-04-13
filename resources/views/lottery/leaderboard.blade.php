@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold text-gold">🏆 Top Winners</h1>
                <div class="flex gap-2">
                    <a href="{{ route('lottery.leaderboard', 'weekly') }}" class="btn-golden text-sm py-1 px-3">Weekly</a>
                    <a href="{{ route('lottery.leaderboard', 'monthly') }}" class="btn-golden text-sm py-1 px-3">Monthly</a>
                    <a href="{{ route('lottery.leaderboard', 'all') }}" class="btn-golden text-sm py-1 px-3">All Time</a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gold/30">
                        <tr><th class="px-4 py-2 text-left">Rank</th><th class="px-4 py-2 text-left">User</th><th class="px-4 py-2 text-left">Total Won (KES)</th></tr>
                    </thead>
                    <tbody>
                        @foreach($topWinners as $index => $winner)
                        <tr class="border-b border-gold/20">
                            <td class="px-4 py-2">{{ $index+1 }}</td>
                            <td class="px-4 py-2">{{ $winner->user->name }}</td>
                            <td class="px-4 py-2 text-green-400">{{ number_format($winner->total_win, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
