@extends('layouts.app')
@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold golden-title mb-6">📈 My Portfolio</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="card-golden p-4"><p class="text-ivory/60">Total Invested</p><p class="text-2xl font-bold text-gold">KES {{ number_format($totalInvested, 2) }}</p></div>
            <div class="card-golden p-4"><p class="text-ivory/60">Total Profit Earned</p><p class="text-2xl font-bold text-green-400">KES {{ number_format($totalProfit, 2) }}</p></div>
            <div class="card-golden p-4"><a href="{{ route('machines.index') }}" class="btn-golden w-full">+ New Investment</a></div>
        </div>
        @if($investments->count())
        <div class="card-golden p-6">
            @foreach($investments->groupBy('type') as $type => $group)
            <h2 class="text-xl font-bold text-gold mt-4 mb-3">{{ $type === 'machine' ? '🤖 RX Machines' : '📜 Legacy Plans' }}</h2>
            <div class="space-y-3">
                @foreach($group as $inv)
                <div class="bg-gold/5 rounded-lg p-4 flex justify-between items-center">
                    <div><strong class="text-gold">{{ $inv['name'] }}</strong>@if(isset($inv['vip_level']))<span class="ml-2 text-xs">VIP {{ $inv['vip_level'] }}</span>@endif<br><span class="text-ivory/60 text-sm">Started: {{ \Carbon\Carbon::parse($inv['start_date'])->format('d M Y') }}</span></div>
                    <div class="text-right"><span class="text-gold">KES {{ number_format($inv['amount'], 2) }}</span><br><span class="text-green-400 text-sm">Daily +KES {{ number_format($inv['daily_profit'], 2) }}</span></div>
                    <div class="text-right"><span class="text-sm {{ $inv['status'] === 'active' ? 'text-green-400' : 'text-gold' }}">{{ ucfirst($inv['status']) }}</span><br><span class="text-xs text-ivory/50">{{ $inv['progress'] }}% complete</span></div>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
        @else <div class="card-golden p-6 text-center"><p class="text-ivory/50">No investments yet. <a href="{{ route('machines.index') }}" class="text-gold-400">Start investing →</a></p></div>
        @endif
    </div>
</div>
@endsection
