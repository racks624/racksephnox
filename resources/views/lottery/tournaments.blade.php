@extends('layouts.app')
@section('content')
<div class="py-12"><div class="max-w-4xl mx-auto"><div class="card-golden p-6"><h1 class="text-2xl font-bold text-gold mb-4">🏆 Tournaments</h1>@if($activeTournament)<div class="bg-gold/10 p-4 rounded mb-4"><h3 class="text-gold">Active: {{ $activeTournament->name }}</h3><p>Ends: {{ $activeTournament->end_date->format('d M Y') }}</p></div><h3 class="text-gold mt-4">Leaderboard</h3><div class="space-y-2">@foreach($leaderboard as $idx => $entry)<div class="flex justify-between p-2 border-b border-gold/20"><span>#{{ $idx+1 }}</span><span>{{ $entry->user->name }}</span><span>KES {{ number_format($entry->total_win,2) }}</span></div>@endforeach</div>@else<p class="text-ivory/50">No active tournaments</p>@endif</div></div></div>
@endsection
