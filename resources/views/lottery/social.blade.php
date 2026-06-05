@extends('layouts.app')
@section('content')
<div class="py-12"><div class="max-w-2xl mx-auto"><div class="card-golden p-6"><h1 class="text-2xl font-bold text-gold mb-4">🔥 Live Wins Feed</h1><div class="space-y-3">@foreach($recentWins as $win)<div class="bg-gold/10 p-3 rounded flex justify-between"><span>🎉 {{ $win->user->name }}</span><span class="text-green-400">+KES {{ number_format($win->win_amount,2) }}</span><span class="text-xs text-ivory/50">{{ $win->created_at->diffForHumans() }}</span></div>@endforeach</div></div></div></div>
@endsection
