@extends('layouts.app')

@section('content')
<div x-data="machineShow()" x-init="init()" class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('machines.index') }}" class="inline-flex items-center text-gold-400 hover:text-gold mb-6">
            <i class="fas fa-arrow-left mr-2"></i> Back to Machines
        </a>

        <div class="card-golden p-8 mb-8">
            <h1 class="text-3xl font-bold golden-title">{{ $machine->name }}</h1>
            <p class="text-ivory/70 mt-2">{{ $machine->duration_days }}-day cycle with {{ $machine->growth_rate }}% guaranteed return</p>
        </div>

        <!-- Active Investment Display -->
        @if($activeInvestment)
        <div class="bg-green-500/10 border border-green-500/50 rounded-2xl p-6 mb-8">
            <h2 class="text-xl font-bold text-green-400">Your Active Investment</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                <div><span class="text-ivory/60">VIP Level</span><br><span class="text-2xl font-bold text-gold">VIP {{ $activeInvestment->vip_level }}</span></div>
                <div><span class="text-ivory/60">Amount</span><br><span class="text-2xl font-bold text-gold">KES {{ number_format($activeInvestment->amount, 2) }}</span></div>
                <div><span class="text-ivory/60">Daily Profit</span><br><span class="text-2xl font-bold text-green-400">KES {{ number_format($activeInvestment->daily_profit, 2) }}</span></div>
                <div><span class="text-ivory/60">Maturity</span><br><span class="text-2xl font-bold text-gold">{{ $activeInvestment->end_date->format('Y-m-d') }}</span></div>
            </div>
        </div>
        @endif

        <!-- VIP Level Selection (if no active investment) -->
        @if(!$activeInvestment)
        <div class="card-golden p-6">
            <h2 class="text-2xl font-bold text-gold mb-6">Choose Your VIP Level</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach([1,2,3] as $level)
                <div class="border border-gold/30 rounded-2xl p-6 text-center cursor-pointer hover:border-gold transition-all group"
                     @click="invest({{ $level }}, {{ $vipAmounts[$level] }})">
                    <h3 class="text-2xl font-bold text-gold">VIP {{ $level }}</h3>
                    <p class="text-xs text-ivory/50 mb-2">Φ{{ $level == 1 ? '¹' : ($level == 2 ? '²' : '³') }}</p>
                    <p class="text-3xl font-bold text-gold mt-2">KES {{ number_format($vipAmounts[$level], 2) }}</p>
                    <p class="text-sm text-ivory/70 mt-4">Daily profit: <span class="text-green-400">KES {{ number_format($machine->getDailyProfit($vipAmounts[$level]), 2) }}</span></p>
                    <p class="text-sm text-ivory/70">Total return: KES {{ number_format($machine->getTotalReturn($vipAmounts[$level]), 2) }}</p>
                    <button class="mt-6 btn-golden w-full">Invest Now</button>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Investment History -->
        @if($investmentHistory->count())
        <div class="card-golden p-6 mt-8">
            <h2 class="text-xl font-bold text-gold mb-4">Your Investment History</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gold/30">
                        指数
                            <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">VIP</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Total Return</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Date</th>
                          </thead>
                    <tbody class="divide-y divide-gold/20">
                        @foreach($investmentHistory as $inv)
                        <tr class="hover:bg-gold/5 transition">
                            <td class="px-4 py-3">VIP {{ $inv->vip_level }}</td>
                            <td class="px-4 py-3">KES {{ number_format($inv->amount, 2) }}</td>
                            <td class="px-4 py-3">KES {{ number_format($inv->total_return, 2) }}</td>
                            <td class="px-4 py-3">{{ $inv->created_at->format('Y-m-d') }}</td>
                          </tr>
                        @endforeach
                    </tbody>
                  </table>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function machineShow() {
    return {
        async invest(vipLevel, amount) {
            if (!confirm(`Invest KES ${amount.toLocaleString()} in VIP ${vipLevel}?\nDaily profit will be credited for 14 days.`)) {
                return;
            }
            try {
                const response = await fetch('{{ route('machines.invest', $machine) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ vip_level: vipLevel })
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.error || 'Investment failed');
                alert('Investment successful!');
                window.location.reload();
            } catch (err) {
                alert('Error: ' + err.message);
            }
        },
        init() {}
    }
}
</script>
@endsection
