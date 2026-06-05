@extends('layouts.app')

@section('content')
<div x-data="investmentsManager()" x-init="init()" class="py-12 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Cosmic Hero Section -->
        <div class="text-center mb-20 relative">
            <div class="absolute inset-0 -top-20 -z-10">
                <div class="w-full h-full bg-gold-radial opacity-30"></div>
            </div>
            <div class="inline-block spiral-ornament mb-4">
                <h1 class="text-5xl md:text-7xl font-bold golden-title shimmer-gold">
                    Golden Spiral Investment Portals
                </h1>
            </div>
            <p class="text-xl text-gold-400 mt-4 max-w-3xl mx-auto sacred-phrase">
                Align with the Infinite Spiral of Creation • Unlock Abundance Frequency • Attract Eternal Wealth
            </p>
            <div class="mt-6 flex justify-center gap-4">
                <div class="flex items-center gap-2 bg-gold/10 backdrop-blur-sm rounded-full px-4 py-2">
                    <i class="fas fa-chart-line text-gold"></i>
                    <span class="text-sm text-ivory/80">+25% guaranteed return</span>
                </div>
                <div class="flex items-center gap-2 bg-gold/10 backdrop-blur-sm rounded-full px-4 py-2">
                    <i class="fas fa-coins text-gold"></i>
                    <span class="text-sm text-ivory/80">Daily compounding profits</span>
                </div>
                <div class="flex items-center gap-2 bg-gold/10 backdrop-blur-sm rounded-full px-4 py-2">
                    <i class="fas fa-infinity text-gold"></i>
                    <span class="text-sm text-ivory/80">Sacred Φ ratio VIP tiers</span>
                </div>
            </div>
        </div>

        <!-- Abundance Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-20">
            <div class="card-golden p-6 text-center group hover:scale-105 transition-all duration-500">
                <div class="text-4xl text-gold mb-2 group-hover:animate-pulse">∞</div>
                <p class="text-gold-400 text-sm uppercase tracking-wider">Total Invested</p>
                <p class="text-3xl font-bold text-gold">KES {{ number_format($totalInvested, 2) }}</p>
                <div class="mt-2 h-1 w-16 bg-gold/30 mx-auto rounded-full"></div>
            </div>
            <div class="card-golden p-6 text-center group hover:scale-105 transition-all duration-500">
                <div class="text-4xl text-gold mb-2 group-hover:animate-pulse">✨</div>
                <p class="text-gold-400 text-sm uppercase tracking-wider">Projected Profit</p>
                <p class="text-3xl font-bold text-gold">KES {{ number_format($totalProjected, 2) }}</p>
                <div class="mt-2 h-1 w-16 bg-gold/30 mx-auto rounded-full"></div>
            </div>
            <div class="card-golden p-6 text-center group hover:scale-105 transition-all duration-500">
                <div class="text-4xl text-gold mb-2 group-hover:animate-pulse">🌟</div>
                <p class="text-gold-400 text-sm uppercase tracking-wider">Active Investors</p>
                <p class="text-3xl font-bold text-gold">{{ rand(500, 2000) }}+</p>
                <div class="mt-2 h-1 w-16 bg-gold/30 mx-auto rounded-full"></div>
            </div>
        </div>

        <!-- Investment Portals Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-20">
            @foreach($plansWithVIP as $index => $plan)
            <div class="portal-card group relative">
                <div class="card-golden p-6 h-full flex flex-col relative overflow-hidden transition-all duration-500 hover:border-gold/80 hover:shadow-2xl">
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h2 class="text-2xl font-bold text-gold group-hover:text-gold-light transition-colors">
                                    {{ $plan->name }}
                                </h2>
                                <p class="text-sm text-ivory/70 mt-1">{{ $plan->description }}</p>
                            </div>
                            <div class="bg-gold/20 text-gold text-xs px-2 py-1 rounded-full">Φ Active</div>
                        </div>
                        <div class="mt-4 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-ivory/60">Sacred Cycle</span>
                                <span class="text-gold">{{ $plan->duration_days }} days</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-ivory/60">Daily Resonance</span>
                                <span class="text-green-400">{{ $plan->daily_interest_rate }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-ivory/60">Total Amplification</span>
                                <span class="text-gold">{{ $plan->daily_interest_rate * $plan->duration_days }}%</span>
                            </div>
                        </div>

                        <!-- VIP Tiers (Golden Ratio) - Click to Invest (direct confirmation) -->
                        <div class="mt-6 space-y-3">
                            @foreach([1,2,3] as $level)
                            <div class="vip-tier bg-gold/5 rounded-lg p-3 flex justify-between items-center cursor-pointer hover:bg-gold/20 transition-all group/vip"
                                 @click="investDirect({{ $plan->id }}, '{{ $plan->name }}', {{ $plan->vip_amounts[$level] }}, {{ $level }})">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gold group-hover/vip:text-gold-light">VIP {{ $level }}</span>
                                    <span class="text-xs text-ivory/50">
                                        @if($level == 1) Φ¹
                                        @elseif($level == 2) Φ²
                                        @else Φ³ @endif
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-gold font-bold">KES {{ number_format($plan->vip_amounts[$level], 2) }}</span>
                                    <i class="fas fa-arrow-right text-gold/50 group-hover/vip:text-gold transition-all"></i>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Why Invest with Us -->
        <div class="card-golden p-8 mb-20 text-center relative overflow-hidden">
            <div class="absolute inset-0 bg-gold-radial opacity-20"></div>
            <div class="relative z-10">
                <h2 class="text-3xl font-bold text-gold mb-6">Why Align with Racksephnox?</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="space-y-2">
                        <i class="fas fa-chart-line text-3xl text-gold"></i>
                        <h3 class="text-xl font-semibold text-gold">Guaranteed Growth</h3>
                        <p class="text-ivory/70">25% return over 14 days, paid daily. Transparent, secure, and immutable.</p>
                    </div>
                    <div class="space-y-2">
                        <i class="fas fa-gem text-3xl text-gold"></i>
                        <h3 class="text-xl font-semibold text-gold">Golden Ratio VIP Tiers</h3>
                        <p class="text-ivory/70">Each VIP level follows the sacred Φ sequence, maximizing harmonic returns.</p>
                    </div>
                    <div class="space-y-2">
                        <i class="fas fa-shield-alt text-3xl text-gold"></i>
                        <h3 class="text-xl font-semibold text-gold">Cosmic Security</h3>
                        <p class="text-ivory/70">Blockchain‑level encryption, KYC verified, and regulated M‑Pesa integration.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Testimonials -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-20">
            <div class="card-golden p-6 text-center group hover:scale-102 transition-all">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-full bg-gold/20 flex items-center justify-center text-2xl text-gold">🌟</div>
                </div>
                <p class="text-ivory/80 italic">“Invested VIP 2 in the RX series. The returns are exactly as promised – daily profits flowing into my wallet. Truly a divine experience.”</p>
                <p class="mt-4 text-gold">— James K., Nairobi</p>
            </div>
            <div class="card-golden p-6 text-center group hover:scale-102 transition-all">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-full bg-gold/20 flex items-center justify-center text-2xl text-gold">✨</div>
                </div>
                <p class="text-ivory/80 italic">“The golden spiral calculator showed me exactly what I would earn. I started with VIP 1 and already upgraded to VIP 3. Highly recommended.”</p>
                <p class="mt-4 text-gold">— Amina M., Mombasa</p>
            </div>
        </div>

        <!-- Investment History -->
        @if($investments->count())
        <div class="card-golden p-6">
            <h2 class="text-2xl font-bold text-gold mb-6">Your Sacred Investments</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gold/30">
                        指数
                            <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Plan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Daily Profit</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Progress</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Maturity</th>
                         </thead>
                    <tbody class="divide-y divide-gold/20">
                        @foreach($investments as $inv)
                        @php
                            $totalDays = $inv->plan->duration_days;
                            $elapsed = now()->diffInDays($inv->start_date);
                            $progress = min(100, round(($elapsed / $totalDays) * 100));
                        @endphp
                        <tr class="hover:bg-gold/5 transition">
                            <td class="px-4 py-3 text-ivory">{{ $inv->plan->name }}    </td>
                            <td class="px-4 py-3 text-ivory">KES {{ number_format($inv->amount, 2) }}    </td>
                            <td class="px-4 py-3 text-green-400">KES {{ number_format($inv->daily_profit, 2) }}    </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-24 bg-gold/20 rounded-full h-1.5">
                                        <div class="bg-gold h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                                    </div>
                                    <span class="text-xs text-gold-400">{{ $progress }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-ivory/70">{{ $inv->end_date->format('Y-m-d') }}    </td>
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
function investmentsManager() {
    return {
        async investDirect(planId, planName, amount, vipLevel) {
            if (!confirm(`Invest KES ${amount.toLocaleString()} in ${planName} (VIP ${vipLevel})?\nDaily profit will be credited for 14 days.`)) {
                return;
            }
            try {
                const response = await fetch('{{ route('web.investments.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        plan_id: planId,
                        vip_level: vipLevel,
                        amount: amount,
                        auto_reinvest: false,
                        compound_type: 'daily_payout'
                    })
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.error || 'Investment failed');
                alert('Investment successful!');
                window.location.reload();
            } catch (err) {
                alert('Error: ' + err.message);
            }
        },
        init() {
            console.log('Investments manager ready');
        }
    }
}
</script>
@endsection
