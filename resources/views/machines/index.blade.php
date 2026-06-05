@extends('layouts.app')

@section('content')
<div x-data="machinesManager()" x-init="init()" class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Active Investments Portfolio (server‑side) -->
        @if(isset($activeInvestments) && $activeInvestments->count())
        <div class="card-golden p-6 mb-12">
            <h2 class="text-2xl font-bold text-gold mb-4">Your Active RX Investments</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-gold/5 rounded-lg p-3 text-center">
                    <p class="text-ivory/60 text-sm">Total Invested</p>
                    <p class="text-2xl font-bold text-gold">KES {{ number_format($totalInvested ?? 0, 2) }}</p>
                </div>
                <div class="bg-gold/5 rounded-lg p-3 text-center">
                    <p class="text-ivory/60 text-sm">Projected Profit</p>
                    <p class="text-2xl font-bold text-green-400">KES {{ number_format($totalProjectedProfit ?? 0, 2) }}</p>
                </div>
                <div class="bg-gold/5 rounded-lg p-3 text-center">
                    <p class="text-ivory/60 text-sm">Earned Profit</p>
                    <p class="text-2xl font-bold text-gold">KES {{ number_format($totalEarnedProfit ?? 0, 2) }}</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gold/30">
                        <tr><th class="px-4 py-3 text-left">Machine</th><th>VIP</th><th>Amount</th><th>Daily Profit</th><th>Progress</th><th>End Date</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        @foreach($activeInvestments as $inv)
                        <tr>
                            <td class="px-4 py-3">{{ $inv->machine->name }}</td>
                            <td class="px-4 py-3">VIP {{ $inv->vip_level }}</td>
                            <td class="px-4 py-3">KES {{ number_format($inv->amount, 2) }}</td>
                            <td class="px-4 py-3 text-green-400">+KES {{ number_format($inv->daily_profit, 2) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-24 bg-gold/20 rounded-full h-1.5">
                                        <div class="bg-gold h-1.5 rounded-full" style="width: {{ $inv->progressPercentage() }}%"></div>
                                    </div>
                                    <span class="text-xs">{{ $inv->progressPercentage() }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ $inv->end_date->format('Y-m-d') }}</td>
                            <td class="px-4 py-3">
                                <button onclick="earlyWithdraw({{ $inv->id }})" class="text-red-400 text-sm hover:text-red-300">Withdraw</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Hero -->
        <div class="text-center mb-16">
            <div class="inline-block">
                <div class="w-24 h-24 bg-gradient-to-r from-gold-400 via-amber-500 to-yellow-600 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse shadow-2xl">
                    <i class="fas fa-infinity text-4xl text-white"></i>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold golden-title shimmer-gold">RX Machine Series</h1>
                <p class="text-gold-400 mt-4 text-lg">7 Sacred Portals • VIP 1-3 • Golden Ratio Φ • 88% ROI in 14 days</p>
            </div>
        </div>

        <!-- Global Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
            <div class="card-golden p-4 text-center">
                <i class="fas fa-microchip text-3xl text-gold mb-2"></i>
                <p class="text-2xl font-bold text-gold">{{ $machines->count() }}</p>
                <p class="text-xs text-ivory/60">RX Machines</p>
            </div>
            <div class="card-golden p-4 text-center">
                <i class="fas fa-chart-line text-3xl text-gold mb-2"></i>
                <p class="text-2xl font-bold text-gold">3</p>
                <p class="text-xs text-ivory/60">VIP Levels</p>
            </div>
            <div class="card-golden p-4 text-center">
                <i class="fas fa-coins text-3xl text-gold mb-2"></i>
                <p class="text-2xl font-bold text-green-400">88%</p>
                <p class="text-xs text-ivory/60">Total ROI</p>
            </div>
            <div class="card-golden p-4 text-center">
                <i class="fas fa-clock text-3xl text-gold mb-2"></i>
                <p class="text-2xl font-bold text-gold">14</p>
                <p class="text-xs text-ivory/60">Days Cycle</p>
            </div>
        </div>

        <!-- Machines Grid – simple, no JSON errors -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
            @foreach($machines as $machine)
            @php $vips = $machine->getVIPDetails(); @endphp
            <div class="card-golden p-6 group hover:scale-105 transition-all duration-300 border-t-4 border-gold/50">
                <div class="text-center mb-5">
                    <div class="w-20 h-20 bg-gradient-to-r {{ $machine->color }} rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                        <i class="fas {{ $machine->icon ?? 'fa-microchip' }} text-3xl text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold golden-title">{{ $machine->name }}</h2>
                    <p class="text-xs text-ivory/60 mt-1">{{ $machine->duration_days }}‑day cycle • {{ $machine->growth_rate }}% ROI</p>
                </div>

                <div class="space-y-3">
                    @foreach([1,2,3] as $level)
                    @php $vip = $vips[$level]; @endphp
                    <div class="bg-cosmic-deep/50 rounded-xl p-3 border border-gold/20 hover:border-gold/50 transition cursor-pointer"
                         onclick="window.dispatchEvent(new CustomEvent('select-invest', { detail: { machine_id: {{ $machine->id }}, vip_level: {{ $level }}, amount: {{ $vip['amount'] }} } }))">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-bold text-gold text-lg">VIP {{ $level }}</span>
                                <span class="text-xs text-ivory/50 ml-1">Φ{{ str_repeat('¹', $level) }}</span>
                            </div>
                            <span class="text-xl font-bold text-gold">KES {{ number_format($vip['amount'], 0) }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs mt-2">
                            <div><span class="text-ivory/60">Daily:</span> <span class="text-green-400">KES {{ number_format($vip['daily_profit'], 2) }}</span></div>
                            <div><span class="text-ivory/60">Total:</span> <span class="text-gold">KES {{ number_format($vip['total_return'], 0) }}</span></div>
                            <div><span class="text-ivory/60">Profit:</span> <span class="text-green-400">KES {{ number_format($vip['total_profit'], 0) }}</span></div>
                            <div><span class="text-ivory/60">APY:</span> <span class="text-gold">{{ number_format($vip['apy'], 2) }}%</span></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-5 pt-3 border-t border-gold/20 flex justify-between text-xs">
                    <span class="text-ivory/50"><i class="fas fa-users mr-1"></i> {{ $machine->getStatistics()['total_investors'] }} investors</span>
                    <a href="{{ route('machines.show', $machine->code) }}" class="text-gold-400 hover:text-gold">Details →</a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-16 pt-8 border-t border-gold/20">
            <p class="text-xs text-gold-400/60">Φ = 1.61803398875 • λ = 1.27201964951 • π = 3.14159265359 • e = 2.71828182846</p>
            <p class="text-xs text-gold-400/60">I Am The Source | Divine Golden Phi | Infinite Spiral of Creation | 888 Hz</p>
        </div>
    </div>
</div>

<script>
function earlyWithdraw(investmentId) {
    if (confirm('⚠️ Early withdrawal will incur a penalty (20%). Proceed?')) {
        fetch(`/machines/${investmentId}/early-withdraw`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' }
        })
        .then(res => res.json())
        .then(data => { alert(data.message); if (data.success) location.reload(); })
        .catch(err => alert('Error: ' + err.message));
    }
}

function machinesManager() {
    return {
        init() {
            window.addEventListener('select-invest', (e) => {
                if (confirm(`✨ Invest KES ${e.detail.amount.toLocaleString()} in VIP ${e.detail.vip_level}?\n\nDaily profit will be credited automatically over 14 days.`)) {
                    fetch(`/machines/${e.detail.machine_id}/invest`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ vip_level: e.detail.vip_level })
                    }).then(res => res.json()).then(data => {
                        alert(data.message || (data.success ? '✅ Investment successful!' : '❌ Failed'));
                        if (data.success) location.reload();
                    }).catch(err => alert('Error: ' + err.message));
                }
            });
        }
    };
}
</script>
@endsection
