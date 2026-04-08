@extends('layouts.app')

@section('content')
<div x-data="rxMachinesManager()" x-init="init()" class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Global Hero -->
        <div class="text-center mb-16">
            <div class="inline-block">
                <div class="w-24 h-24 bg-gradient-to-r from-gold-400 via-amber-500 to-yellow-600 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse shadow-2xl">
                    <i class="fas fa-infinity text-4xl text-white"></i>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold golden-title shimmer-gold">RX Machine Series</h1>
                <p class="text-gold-400 mt-4 text-lg">6 Sacred Portals • 3 VIP Levels • Golden Ratio Φ • Up to 35% ROI</p>
                <p class="text-sm text-ivory/50 mt-2">Trusted by investors in 🇰🇪 Kenya • 🇺🇸 USA • 🇬🇧 UK • 🇦🇪 UAE • 🇮🇳 India • 🇿🇦 South Africa</p>
            </div>
        </div>

        <!-- Global Stats Widget -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
            <div class="card-golden p-4 text-center group hover:scale-105 transition">
                <i class="fas fa-microchip text-3xl text-gold mb-2"></i>
                <p class="text-2xl font-bold text-gold">6</p>
                <p class="text-xs text-ivory/60">RX Machines</p>
            </div>
            <div class="card-golden p-4 text-center group hover:scale-105 transition">
                <i class="fas fa-chart-line text-3xl text-gold mb-2"></i>
                <p class="text-2xl font-bold text-gold">3</p>
                <p class="text-xs text-ivory/60">VIP Levels</p>
            </div>
            <div class="card-golden p-4 text-center group hover:scale-105 transition">
                <i class="fas fa-coins text-3xl text-gold mb-2"></i>
                <p class="text-2xl font-bold text-green-400">35%</p>
                <p class="text-xs text-ivory/60">Max ROI</p>
            </div>
            <div class="card-golden p-4 text-center group hover:scale-105 transition">
                <i class="fas fa-clock text-3xl text-gold mb-2"></i>
                <p class="text-2xl font-bold text-gold">14</p>
                <p class="text-xs text-ivory/60">Days Cycle</p>
            </div>
        </div>

        <!-- RX Machines Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
            @foreach($machines as $machine)
            @php
                $vips = $machine->getVIPDetails();
                $stats = $machine->getStatistics();
            @endphp
            <div class="card-golden p-6 group hover:scale-105 transition-all duration-300 border-t-4 border-gold/50">
                <!-- Header -->
                <div class="text-center mb-5">
                    <div class="w-20 h-20 bg-gradient-to-r {{ $machine->color }} rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                        <i class="fas {{ $machine->icon ?? 'fa-microchip' }} text-3xl text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold golden-title">{{ $machine->name }}</h2>
                    <p class="text-xs text-ivory/60 mt-1">{{ $machine->duration_days }}‑day cycle • {{ $machine->growth_rate }}% ROI</p>
                    <p class="text-xs mt-1 px-2 py-0.5 inline-block rounded-full bg-gold/20 text-gold">{{ $machine->risk_profile }} Risk</p>
                </div>

                <!-- VIP Tiers -->
                <div class="space-y-3">
                    @foreach([1, 2, 3] as $level)
                    @php $vip = $vips[$level]; @endphp
                    <div class="bg-cosmic-deep/50 rounded-xl p-3 border border-gold/20 hover:border-gold/50 transition cursor-pointer" onclick="window.dispatchEvent(new CustomEvent('select-invest', { detail: { machine_id: {{ $machine->id }}, vip_level: {{ $level }}, amount: {{ $vip['amount'] }} } }))">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-bold text-gold text-lg">VIP {{ $level }}</span>
                                <span class="text-xs text-ivory/50 ml-1">Φ{{ $vip['phi_power'] }}</span>
                            </div>
                            <span class="text-xl font-bold text-gold">KES {{ number_format($vip['amount'], 0) }}</span>
                        </div>
                        <div class="flex justify-between text-xs mt-2">
                            <span class="text-ivory/60">Daily: <span class="text-green-400">KES {{ number_format($vip['daily_profit'], 2) }}</span></span>
                            <span class="text-ivory/60">Total: <span class="text-gold">KES {{ number_format($vip['total_return'], 0) }}</span></span>
                            <span class="text-ivory/60">Profit: <span class="text-green-400">KES {{ number_format($vip['total_profit'], 0) }}</span></span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Stats Footer -->
                <div class="mt-5 pt-3 border-t border-gold/20 flex justify-between text-xs">
                    <span class="text-ivory/50"><i class="fas fa-users mr-1"></i> {{ $stats['total_investors'] }} investors</span>
                    <span class="text-ivory/50"><i class="fas fa-coins mr-1"></i> KES {{ number_format($stats['total_invested'] / 1000, 0) }}K</span>
                    <a href="{{ route('machines.show', $machine->code) }}" class="text-gold-400 hover:text-gold">Details →</a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Sacred Footer -->
        <div class="text-center mt-16 pt-8 border-t border-gold/20">
            <p class="text-xs text-gold-400/60">I Am The Source | Divine Golden Phi | Infinite Spiral of Creation | 888 Hz</p>
            <p class="text-xs text-gold-500/40 mt-1">Racksephnox – Global Crypto Investment Platform</p>
        </div>
    </div>
</div>

<script>
function rxMachinesManager() {
    return {
        init() {
            window.addEventListener('select-invest', (e) => {
                if (confirm(`✨ Invest KES ${e.detail.amount.toLocaleString()} in VIP ${e.detail.vip_level}?`)) {
                    fetch(`/machines/${e.detail.machine_id}/invest`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ vip_level: e.detail.vip_level })
                    }).then(res => res.json()).then(data => {
                        alert(data.message || (data.success ? '✅ Investment successful!' : '❌ Failed'));
                        if (data.success) location.reload();
                    }).catch(err => alert('Error: ' + err.message));
                }
            });
        }
    }
}
</script>
@endsection
