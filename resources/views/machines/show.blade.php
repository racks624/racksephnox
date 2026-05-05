@extends('layouts.app')

@section('content')
<div x-data="machineShowManager()" x-init="init()" class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('machines.index') }}" class="inline-flex items-center text-gold-400 hover:text-gold mb-6 transition group">
            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition"></i> Back to Machines
        </a>

        <div class="card-golden p-8 mb-8 text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gold/5 rounded-full blur-2xl"></div>
            <div class="w-24 h-24 bg-gradient-to-r {{ $machine->color }} rounded-2xl flex items-center justify-center mx-auto mb-4 animate-pulse">
                <i class="fas {{ $machine->icon ?? 'fa-microchip' }} text-4xl text-white"></i>
            </div>
            <h1 class="text-4xl font-bold golden-title">{{ $machine->name }}</h1>
            <p class="text-gold-400 mt-2">{{ $machine->description ?? $machine->duration_days }}-day sacred cycle with {{ $machine->growth_rate }}% guaranteed return (88% total profit).</p>
            <div class="flex justify-center gap-3 mt-4">
                <span class="px-3 py-1 bg-gold/20 rounded-full text-xs text-gold"><i class="fas fa-calendar mr-1"></i> {{ $machine->duration_days }} Days</span>
                <span class="px-3 py-1 bg-gold/20 rounded-full text-xs text-gold"><i class="fas fa-chart-line mr-1"></i> {{ $machine->growth_rate }}% ROI</span>
                <span class="px-3 py-1 bg-gold/20 rounded-full text-xs text-gold"><i class="fas fa-percent mr-1"></i> 12.6% Daily</span>
            </div>
        </div>

        @if($activeInvestment)
        <div class="bg-gradient-to-r from-green-500/10 to-emerald-500/10 border border-green-500/50 rounded-2xl p-6 mb-8">
            <h2 class="text-xl font-bold text-green-400 flex items-center gap-2"><i class="fas fa-check-circle animate-pulse"></i> Your Active Investment</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-3">
                <div><p class="text-ivory/60 text-sm">VIP Level</p><p class="text-2xl font-bold text-gold">VIP {{ $activeInvestment->vip_level }}</p></div>
                <div><p class="text-ivory/60 text-sm">Amount</p><p class="text-2xl font-bold text-gold">KES {{ number_format($activeInvestment->amount, 2) }}</p></div>
                <div><p class="text-ivory/60 text-sm">Daily Profit</p><p class="text-2xl font-bold text-green-400">KES {{ number_format($activeInvestment->daily_profit, 2) }}</p></div>
                <div><p class="text-ivory/60 text-sm">Maturity Date</p><p class="text-lg font-bold text-gold">{{ $activeInvestment->end_date->format('d M Y') }}</p></div>
            </div>
            <div class="mt-4"><div class="flex justify-between text-xs mb-1"><span class="text-ivory/60">Progress</span><span class="text-gold">{{ $activeInvestment->progress_percentage }}%</span></div><div class="w-full bg-gold/20 rounded-full h-2"><div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $activeInvestment->progress_percentage }}%"></div></div></div>
        </div>
        @else
        <div class="card-golden p-6">
            <h2 class="text-2xl font-bold text-gold mb-6 text-center">Choose Your VIP Portal</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($vipDetails as $level => $vip)
                <div class="border-2 border-gold/30 rounded-2xl p-6 text-center cursor-pointer hover:border-gold transition-all group hover:scale-105"
                     @click="invest({{ $level }}, {{ $vip['amount'] }})">
                    <div class="w-16 h-16 bg-gradient-to-r from-gold-400/20 to-gold-600/20 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition"><i class="fas fa-crown text-2xl text-gold"></i></div>
                    <h3 class="text-2xl font-bold text-gold">VIP {{ $level }}</h3>
                    <p class="text-3xl font-bold text-gold mt-2">KES {{ number_format($vip['amount'], 2) }}</p>
                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-ivory/60">Daily profit:</span><span class="text-green-400 font-semibold">KES {{ number_format($vip['daily_profit'], 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-ivory/60">Total return:</span><span class="text-gold font-semibold">KES {{ number_format($vip['total_return'], 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-ivory/60">Total profit:</span><span class="text-green-400 font-semibold">KES {{ number_format($vip['total_profit'], 2) }}</span></div>
                    </div>
                    <button class="mt-6 btn-golden w-full py-3"><i class="fas fa-gem mr-2"></i> Invest Now</button>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="text-center mt-10 pt-6 border-t border-gold/20">
            <p class="text-xs text-gold-400/60">Divine Eternal Universal Frequencies | Guardian and Protector | 888 Hz</p>
        </div>
    </div>
</div>

<script>
function machineShowManager() {
    return {
        async invest(vipLevel, amount) {
            if (!confirm(`✨ Invest KES ${amount.toLocaleString()} in VIP ${vipLevel}?\n\n📈 Daily profit: ~12.6%\n💰 Total return: 88% after 14 days\n\nDaily profit will be credited automatically.`)) return;
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
</script>
@endsection
