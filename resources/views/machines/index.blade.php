@extends('layouts.app')

@section('content')
<div x-data="machinesIndex()" x-init="init()" class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold golden-title">Machine Series</h1>
            <p class="text-gold-400 mt-2">Choose your investment portal – each follows the sacred Golden Ratio Φ</p>
        </div>

        <!-- Active Investments Summary -->
        @if($activeInvestments->count())
        <div class="bg-gold/10 rounded-2xl p-6 mb-10">
            <h2 class="text-xl font-bold text-gold mb-4">Your Active Machines</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($activeInvestments as $inv)
                <div class="bg-cosmic-deep rounded-lg p-4 border border-gold/30">
                    <p class="font-semibold text-gold">{{ $inv->machine->name }} - VIP {{ $inv->vip_level }}</p>
                    <p class="text-sm">Amount: KES {{ number_format($inv->amount, 2) }}</p>
                    <p class="text-sm">Daily Profit: KES {{ number_format($inv->daily_profit, 2) }}</p>
                    <p class="text-sm">Ends: {{ $inv->end_date->format('Y-m-d') }}</p>
                    <a href="{{ route('machines.show', $inv->machine->code) }}" class="mt-2 inline-block text-sm text-gold-400 hover:text-gold">View Details →</a>
                </div>
                @endforeach
            </div>
            <div class="mt-4 text-right">
                <p class="text-sm text-gold-400">Total Invested: KES {{ number_format($totalInvested, 2) }} | Projected Profit: KES {{ number_format($totalProjectedProfit, 2) }}</p>
            </div>
        </div>
        @endif

        <!-- Machines Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($machines as $machine)
            <div class="card-golden p-6 group hover:scale-105 transition-all">
                <h2 class="text-2xl font-bold text-gold">{{ $machine->name }}</h2>
                <p class="text-sm text-ivory/70 mt-1">14‑day cycle • 25% ROI</p>
                <div class="mt-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-ivory/60">VIP 1 (Φ¹)</span>
                        <span class="text-gold">KES {{ number_format($machine->getVIPAmounts()[1], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ivory/60">VIP 2 (Φ²)</span>
                        <span class="text-gold">KES {{ number_format($machine->getVIPAmounts()[2], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ivory/60">VIP 3 (Φ³)</span>
                        <span class="text-gold">KES {{ number_format($machine->getVIPAmounts()[3], 2) }}</span>
                    </div>
                </div>
                <a href="{{ route('machines.show', $machine->code) }}" class="mt-6 block w-full text-center btn-golden">Select Portal →</a>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
function machinesIndex() {
    return { init() {} }
}
</script>
@endsection
