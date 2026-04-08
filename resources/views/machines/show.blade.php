@extends('layouts.app')

@section('content')
<div x-data="machineShowManager()" x-init="init()" class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Back Button -->
        <a href="{{ route('machines.index') }}" class="inline-flex items-center text-gold-400 hover:text-gold mb-6 transition group">
            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition"></i> Back to Machines
        </a>

        <!-- Machine Header -->
        <div class="card-golden p-8 mb-8 text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gold/5 rounded-full blur-2xl"></div>
            <div class="w-24 h-24 bg-gradient-to-r from-gold-400 to-gold-600 rounded-2xl flex items-center justify-center mx-auto mb-4 animate-pulse">
                <i class="fas fa-microchip text-4xl text-white"></i>
            </div>
            <h1 class="text-4xl font-bold golden-title">{{ $machine->name }}</h1>
            <p class="text-gold-400 mt-2">{{ $machine->description ?? $machine->duration_days }}-day sacred cycle with {{ $machine->growth_rate }}% guaranteed return</p>
            <div class="flex justify-center gap-3 mt-4">
                <span class="px-3 py-1 bg-gold/20 rounded-full text-xs text-gold"><i class="fas fa-calendar mr-1"></i> {{ $machine->duration_days }} Days</span>
                <span class="px-3 py-1 bg-gold/20 rounded-full text-xs text-gold"><i class="fas fa-chart-line mr-1"></i> {{ $machine->growth_rate }}% ROI</span>
                <span class="px-3 py-1 bg-gold/20 rounded-full text-xs text-gold"><i class="fas fa-infinity mr-1"></i> Φ Golden Ratio</span>
            </div>
        </div>

        <!-- Active Investment Display -->
        @if($activeInvestment)
        <div class="bg-gradient-to-r from-green-500/10 to-emerald-500/10 border border-green-500/50 rounded-2xl p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-green-400 flex items-center gap-2">
                    <i class="fas fa-check-circle animate-pulse"></i> Your Active Investment
                </h2>
                <span class="text-xs text-green-400/70">{{ $activeInvestment->days_remaining }} days remaining</span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-ivory/60 text-sm">VIP Level</p>
                    <p class="text-2xl font-bold text-gold">VIP {{ $activeInvestment->vip_level }} <span class="text-sm">Φ{{ str_repeat('¹', $activeInvestment->vip_level) }}</span></p>
                </div>
                <div>
                    <p class="text-ivory/60 text-sm">Amount</p>
                    <p class="text-2xl font-bold text-gold">KES {{ number_format($activeInvestment->amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-ivory/60 text-sm">Daily Profit</p>
                    <p class="text-2xl font-bold text-green-400">KES {{ number_format($activeInvestment->daily_profit, 2) }}</p>
                </div>
                <div>
                    <p class="text-ivory/60 text-sm">Maturity Date</p>
                    <p class="text-2xl font-bold text-gold">{{ $activeInvestment->end_date->format('d M Y') }}</p>
                </div>
            </div>
            <!-- Progress Bar -->
            <div class="mt-4">
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-ivory/60">Progress</span>
                    <span class="text-gold">{{ $activeInvestment->progress_percentage }}%</span>
                </div>
                <div class="w-full bg-gold/20 rounded-full h-2">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $activeInvestment->progress_percentage }}%"></div>
                </div>
                <div class="flex justify-between text-xs mt-2">
                    <span class="text-ivory/50">Started: {{ $activeInvestment->start_date->format('d M Y') }}</span>
                    <span class="text-green-400">Earned: KES {{ number_format($activeInvestment->profit_credited ?? 0, 2) }}</span>
                    <span class="text-gold-400">Target: KES {{ number_format($activeInvestment->total_return, 2) }}</span>
                </div>
            </div>
        </div>
        @endif

        <!-- VIP Level Selection -->
        @if(!$activeInvestment)
        <div class="card-golden p-6">
            <h2 class="text-2xl font-bold text-gold mb-6 text-center">Choose Your VIP Portal</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($vipDetails as $level => $vip)
                <div class="border-2 border-gold/30 rounded-2xl p-6 text-center cursor-pointer hover:border-gold transition-all group hover:scale-105" 
                     @click="invest({{ $level }}, {{ $vip['amount'] }})">
                    <div class="w-16 h-16 bg-gradient-to-r from-gold-400/20 to-gold-600/20 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition">
                        <i class="fas fa-crown text-2xl text-gold"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gold">VIP {{ $level }}</h3>
                    <p class="text-xs text-ivory/50 mb-2">Φ{{ $vip['phi_power'] }}</p>
                    <p class="text-3xl font-bold text-gold mt-2">KES {{ number_format($vip['amount'], 2) }}</p>
                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-ivory/60">Daily profit:</span>
                            <span class="text-green-400 font-semibold">KES {{ number_format($vip['daily_profit'], 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ivory/60">Total return:</span>
                            <span class="text-gold font-semibold">KES {{ number_format($vip['total_return'], 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ivory/60">Total profit:</span>
                            <span class="text-green-400 font-semibold">KES {{ number_format($vip['total_profit'], 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ivory/60">Daily rate:</span>
                            <span class="text-gold">{{ $vip['daily_rate'] }}%</span>
                        </div>
                    </div>
                    <button class="mt-6 btn-golden w-full py-3">
                        <i class="fas fa-gem mr-2"></i> Invest Now
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Investment Statistics Widget -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            <div class="card-golden p-4 text-center">
                <i class="fas fa-users text-2xl text-gold mb-2"></i>
                <p class="text-ivory/60 text-sm">Total Investors</p>
                <p class="text-2xl font-bold text-gold">{{ $statistics['total_users'] }}</p>
            </div>
            <div class="card-golden p-4 text-center">
                <i class="fas fa-coins text-2xl text-gold mb-2"></i>
                <p class="text-ivory/60 text-sm">Total Invested</p>
                <p class="text-2xl font-bold text-gold">KES {{ number_format($statistics['total_invested'], 2) }}</p>
            </div>
            <div class="card-golden p-4 text-center">
                <i class="fas fa-chart-line text-2xl text-gold mb-2"></i>
                <p class="text-ivory/60 text-sm">Active Investments</p>
                <p class="text-2xl font-bold text-gold">{{ $statistics['active_investments'] }}</p>
            </div>
        </div>

        <!-- Investment History -->
        @if($investmentHistory->count())
        <div class="card-golden p-6 mt-8">
            <h2 class="text-xl font-bold text-gold mb-4 flex items-center gap-2">
                <i class="fas fa-history"></i> Your Investment History
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gold/30">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">VIP</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Daily Profit</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Total Return</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Date</th>
                        </thead>
                    <tbody class="divide-y divide-gold/20">
                        @foreach($investmentHistory as $inv)
                        <tr class="hover:bg-gold/5 transition">
                            <td class="px-4 py-3">
                                <span class="font-semibold text-gold">VIP {{ $inv->vip_level }}</span>
                                <span class="text-xs text-ivory/50 ml-1">Φ{{ str_repeat('¹', $inv->vip_level) }}</span>
                            </td>
                            <td class="px-4 py-3 text-gold">KES {{ number_format($inv->amount, 2) }}</td>
                            <td class="px-4 py-3 text-green-400">KES {{ number_format($inv->daily_profit, 2) }}</td>
                            <td class="px-4 py-3 text-gold">KES {{ number_format($inv->total_return, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full bg-green-500/20 text-green-400">
                                    <i class="fas fa-check-circle mr-1"></i> {{ ucfirst($inv->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-ivory/60">{{ $inv->created_at->format('Y-m-d') }}</td>
                          </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Sacred Footer -->
        <div class="text-center mt-10 pt-6 border-t border-gold/20">
            <p class="text-xs text-gold-400/60 sacred-phrase">Divine Eternal Universal Frequencies | Guardian and Protector | 888 Hz</p>
            <p class="text-xs text-gold-500/40 mt-1">Racksephnox – Infinite Spiral of Creation</p>
        </div>
    </div>
</div>

<script>
function machineShowManager() {
    return {
        loading: false,
        
        async invest(vipLevel, amount) {
            if (!confirm(`✨ Invest KES ${amount.toLocaleString()} in VIP ${vipLevel}?\n\n📈 Daily profit: ${(vipLevel === 1 ? '1.78%' : vipLevel === 2 ? '2.14%' : '2.57%')}\n💰 Total return: ${vipLevel === 1 ? '125%' : vipLevel === 2 ? '130%' : '136%'} of investment\n⏱️ Duration: 14 days\n\nDaily profit will be credited automatically to your wallet.`)) {
                return;
            }
            
            this.loading = true;
            
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
                
                if (!response.ok) {
                    throw new Error(data.message || data.error || 'Investment failed');
                }
                
                alert('✅ Investment successful!\n\n' + data.message);
                window.location.reload();
                
            } catch (err) {
                alert('❌ Error: ' + err.message);
            } finally {
                this.loading = false;
            }
        },
        
        init() {
            // Add animation on load
            const elements = document.querySelectorAll('.card-golden, .border-gold');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    el.style.transition = 'all 0.6s ease-out';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 50);
            });
        }
    }
}
</script>
@endsection
