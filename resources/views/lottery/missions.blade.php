@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <h1 class="text-2xl font-bold text-gold mb-4">🎯 Daily Missions</h1>
            <p class="text-ivory/70 mb-6">Complete missions to earn free spins and bonus KES!</p>
            <div class="space-y-4">
                @foreach($missions as $mission)
                <div class="admin-card p-4 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-ivory">{{ $mission['name'] }}</h3>
                        <p class="text-sm text-ivory/60">{{ $mission['description'] }}</p>
                        <div class="w-48 bg-gray-700 rounded-full h-2 mt-2">
                            <div class="bg-gold-500 h-2 rounded-full" style="width: {{ min(100, ($mission['progress'] / $mission['target']) * 100) }}%"></div>
                        </div>
                        <p class="text-xs text-gold-400 mt-1">{{ $mission['progress'] }}/{{ $mission['target'] }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-gold-400 text-sm">Reward: {{ $mission['reward'] }}</span>
                        @if($mission['completed'])
                            <div class="text-green-400 text-sm mt-1">✅ Completed</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
