@extends('layouts.app')
@section('content')
<div class="py-12"><div class="max-w-4xl mx-auto"><div class="card-golden p-6"><h1 class="text-2xl font-bold text-gold mb-4">🏅 Achievements</h1><div class="grid grid-cols-1 md:grid-cols-2 gap-4">@foreach($allAchievements as $ach)<div class="bg-gold/10 rounded-lg p-4"><div class="flex items-center gap-3"><i class="fas {{ $ach->icon }} text-2xl text-gold"></i><div><h3 class="font-bold text-gold">{{ $ach->name }}</h3><p class="text-xs text-ivory/60">{{ $ach->description }}</p></div></div><div class="mt-2 text-right">@if($achievements->contains('achievement_id', $ach->id))<span class="text-green-400 text-sm">✓ Earned</span>@else<span class="text-ivory/50 text-sm">Not yet</span>@endif</div></div>@endforeach</div></div></div></div>
@endsection
