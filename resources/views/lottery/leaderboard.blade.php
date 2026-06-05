@extends('layouts.app')
@section('content')
<div class="py-12"><div class="max-w-4xl mx-auto"><div class="card-golden p-6"><h1 class="text-2xl font-bold text-gold mb-4">🏆 {{ ucfirst($period) }} Leaderboard</h1><div class="space-y-2">@foreach($topWinners as $idx => $winner)<div class="flex justify-between items-center p-3 bg-gold/5 rounded"><span class="font-bold">#{{ $idx+1 }}</span><span>{{ $winner->user->name }}</span><span class="text-gold">KES {{ number_format($winner->total_win,2) }}</span></div>@endforeach</div></div></div></div>
@endsection
