@extends('layouts.app')

@section('content')
<div x-data="machineShowManager()" x-init="init()" class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('machines.index') }}" class="inline-flex items-center text-gold-400 hover:text-gold mb-6 transition group">
            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition"></i> Back to Machines
        </a>

        <!-- Machine Hero (with sacred constants) -->
        <div class="card-golden p-8 mb-8 text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gold/5 rounded-full blur-2xl"></div>
            <div class="w-24 h-24 bg-gradient-to-r {{ $machine->color }} rounded-2xl flex items-center justify-center mx-auto mb-4 animate-pulse">
                <i class="fas {{ $machine->icon ?? 'fa-microchip' }} text-4xl text-white"></i>
            </div>
            <h1 class="text-4xl font-bold golden-title">{{ $machine->name }}</h1>
            <p class="text-gold-400 mt-2">{{ $machine->description ?? $machine->duration_days }}-day sacred cycle with {{ $machine->growth_rate }}% guaranteed return (Φ² amplified).</p>
            <div class="flex flex-wrap justify-center gap-3 mt-4">
                <span class="px-3 py-1 bg-gold/20 rounded-full text-xs text-gold"><i class="fas fa-calendar mr-1"></i> {{ $machine->duration_days }} Days</span>
                <span class="px-3 py-1 bg-gold/20 rounded-full text-xs text-gold"><i class="fas fa-chart-line mr-1"></i> {{ $machine->growth_rate }}% ROI</span>
                <span class="px-3 py-1 bg-gold/20 rounded-full text-xs text-gold"><i class="fas fa-percent mr-1"></i> {{ round($machine->growth_rate / $machine->duration_days, 2) }}% Daily</span>
                @if($machine->total_invested_limit)
                <span class="px-3 py-1 bg-gold/20 rounded-full text-xs text-gold"><i class="fas fa-chart-simple mr-1"></i> Limit: KES {{ number_format($machine->total_invested_limit, 0) }}</span>
                @endif
            </div>
        </div>

        <!-- ========== ENHANCED ACTIVE INVESTMENT SECTION ========== -->
        @if($activeInvestment)
        <div class="bg-gradient-to-r from-green-500/10 to-emerald-500/10 border border-green-500/50 rounded-2xl p-6 mb-8">
            <h2 class="text-xl font-bold text-green-400 flex items-center gap-2">
                <i class="fas fa-check-circle animate-pulse"></i> Your Active Investment
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                <div><p class="text-ivory/60 text-sm">VIP Level</p><p class="text-2xl font-bold text-gold">VIP {{ $activeInvestment->vip_level }}</p></div>
                <div><p class="text-ivory/60 text-sm">Amount</p><p class="text-2xl font-bold text-gold">KES {{ number_format($activeInvestment->amount, 2) }}</p></div>
                <div><p class="text-ivory/60 text-sm">Daily Profit</p><p class="text-2xl font-bold text-green-400">KES {{ number_format($activeInvestment->daily_profit, 2) }}</p></div>
                <div><p class="text-ivory/60 text-sm">Maturity Date</p><p class="text-lg font-bold text-gold">{{ $activeInvestment->end_date->format('d M Y') }}</p></div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-ivory/60">Progress (Φ spiral)</span>
                    <span class="text-gold">{{ $activeInvestment->progressPercentage() }}%</span>
                </div>
                <div class="w-full bg-gold/20 rounded-full h-2">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $activeInvestment->progressPercentage() }}%"></div>
                </div>
                <p class="text-xs text-ivory/50 mt-2 text-right">{{ $activeInvestment->daysRemaining() }} days remaining</p>
            </div>
            <div class="mt-4 text-right">
                <button onclick="earlyWithdraw({{ $activeInvestment->id }})" class="text-red-400 text-sm hover:text-red-300 bg-red-500/10 px-4 py-2 rounded-lg transition">
                    <i class="fas fa-sign-out-alt mr-1"></i> Early Withdrawal ({{ $machine->early_withdrawal_penalty ?? 20 }}% penalty)
                </button>
            </div>
        </div>
        @endif

        <!-- ========== VIP PORTALS (with full algorithm details) ========== -->
        @if(!$activeInvestment)
        <div class="card-golden p-6">
            <h2 class="text-2xl font-bold text-gold mb-6 text-center">Choose Your VIP Portal</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($vipDetails as $level => $vip)
                <div class="border-2 border-gold/30 rounded-2xl p-6 text-center cursor-pointer hover:border-gold transition-all group hover:scale-105"
                     @click="invest({{ $level }}, {{ $vip['amount'] }})">
                    <div class="w-16 h-16 bg-gradient-to-r from-gold-400/20 to-gold-600/20 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition">
                        <i class="fas {{ $vip['icon'] }} text-2xl text-gold"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gold">VIP {{ $level }} ({{ $vip['name'] }})</h3>
                    <p class="text-xs text-gold-400 mb-1">Φ{{ $vip['phi_power'] }} • Multiplier: {{ $vip['multiplier'] }}x</p>
                    <p class="text-3xl font-bold text-gold mt-2">KES {{ number_format($vip['amount'], 2) }}</p>
                    <div class="mt-4 space-y-1 text-sm text-left">
                        <div class="flex justify-between"><span class="text-ivory/60">Daily profit:</span><span class="text-green-400 font-semibold">KES {{ number_format($vip['daily_profit'], 2) }} ({{ $vip['daily_rate'] }}%)</span></div>
                        <div class="flex justify-between"><span class="text-ivory/60">Total return:</span><span class="text-gold font-semibold">KES {{ number_format($vip['total_return'], 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-ivory/60">Total profit:</span><span class="text-green-400 font-semibold">KES {{ number_format($vip['total_profit'], 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-ivory/60">Compound return:</span><span class="text-gold">KES {{ number_format($vip['compound_return'], 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-ivory/60">APY:</span><span class="text-gold">{{ number_format($vip['apy'], 2) }}%</span></div>
                        <div class="flex justify-between"><span class="text-ivory/60">Staking reward:</span><span class="text-gold">{{ $vip['staking_reward'] }}%</span></div>
                        <div class="flex justify-between"><span class="text-ivory/60">Referral bonus:</span><span class="text-gold">{{ $vip['referral_bonus'] }}%</span></div>
                    </div>
                    <button class="mt-6 btn-golden w-full py-3"><i class="fas fa-gem mr-2"></i> Invest Now</button>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- ========== INVESTMENT HISTORY (completed investments) ========== -->
        @if(isset($investmentHistory) && $investmentHistory->count())
        <div class="card-golden p-6 mt-8">
            <h3 class="text-xl font-bold text-gold mb-4">Previous Investments (Completed Cycles)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-gold/30">
                        <tr><th class="px-4 py-2 text-left">VIP</th><th>Amount</th><th>Total Return</th><th>Profit</th><th>End Date</th></tr>
                    </thead>
                    <tbody>
                        @foreach($investmentHistory as $inv)
                        <tr>
                            <td class="px-4 py-2">VIP {{ $inv->vip_level }}</td>
                            <td class="px-4 py-2">KES {{ number_format($inv->amount, 2) }}</td>
                            <td class="px-4 py-2 text-gold">KES {{ number_format($inv->total_return ?? $inv->amount + ($inv->daily_profit * $machine->duration_days), 2) }}</td>
                            <td class="px-4 py-2 text-green-400">KES {{ number_format(($inv->total_return ?? $inv->amount + ($inv->daily_profit * $machine->duration_days)) - $inv->amount, 2) }}</td>
                            <td class="px-4 py-2 text-ivory/70">{{ $inv->end_date->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Sacred Footer -->
        <div class="text-center mt-10 pt-6 border-t border-gold/20">
            <p class="text-xs text-gold-400/60">Divine Eternal Universal Frequencies | Guardian and Protector | 888 Hz</p>
            <p class="text-xs text-gold-500/40 mt-1">Powered by Φ, λ, π, e – the sacred constants of creation.</p>
        </div>
    </div>
</div>

<script>
function machineShowManager() {
    return {
        async invest(vipLevel, amount) {
            if (!confirm(`✨ Invest KES ${amount.toLocaleString()} in VIP ${vipLevel}?\n\n📈 Daily profit: ~${Math.round({{ $machine->growth_rate / $machine->duration_days }} * 100) / 100}%\n💰 Total return: {{ $machine->growth_rate }}% after {{ $machine->duration_days }} days\n\nDaily profit will be credited automatically.`)) return;
            try {
                const response = await fetch('{{ route("machines.invest", $machine) }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ vip_level: vipLevel })
                });
                const data = await response.json();
                alert(data.message || (data.success ? '✅ Investment successful!' : '❌ Failed'));
                if (data.success) location.reload();
            } catch (err) { alert('Error: ' + err.message); }
        },
        init() {
            const elements = document.querySelectorAll('.card-golden');
            elements.forEach((el, idx) => { el.style.opacity = '0'; el.style.transform = 'translateY(20px)'; setTimeout(() => { el.style.transition = 'all 0.6s ease-out'; el.style.opacity = '1'; el.style.transform = 'translateY(0)'; }, idx * 50); });
        }
    };
}

function earlyWithdraw(investmentId) {
    if (confirm('⚠️ Early withdrawal will incur a penalty (20%). Proceed?')) {
        fetch(`/machines/${investmentId}/early-withdraw`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
        })
        .then(res => res.json())
        .then(data => { alert(data.message); if (data.success) location.reload(); })
        .catch(err => alert('Error: ' + err.message));
    }
}
</script>
@endsection
