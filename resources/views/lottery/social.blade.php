@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <h1 class="text-2xl font-bold text-gold mb-4">🌍 Live Wins Feed</h1>
            <div class="space-y-3">
                @foreach($recentWins as $win)
                <div class="admin-card p-3 flex justify-between items-center">
                    <div>
                        <span class="font-bold text-ivory">{{ $win->user->name }}</span>
                        <span class="text-gold-400"> won </span>
                        <span class="text-green-400 font-bold">KES {{ number_format($win->win_amount, 2) }}</span>
                    </div>
                    <div class="text-xs text-ivory/50">{{ $win->created_at->diffForHumans() }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
