<!-- Enterprise RX Machines Widget -->
<div x-data="enterpriseRXManager()" x-init="init()" class="enterprise-dashboard">
    
    <!-- Global Stats Header -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-8">
        <div class="luxury-card p-4 text-center">
            <div class="text-2xl mb-2">🏦</div>
            <div class="text-2xl font-bold text-gold" x-text="formatNumber(globalStats.total_invested)"></div>
            <div class="text-xs text-ivory/50">Total Invested (KES)</div>
        </div>
        <div class="luxury-card p-4 text-center">
            <div class="text-2xl mb-2">📈</div>
            <div class="text-2xl font-bold text-green-400" x-text="formatNumber(globalStats.total_profit)"></div>
            <div class="text-xs text-ivory/50">Total Profit (KES)</div>
        </div>
        <div class="luxury-card p-4 text-center">
            <div class="text-2xl mb-2">👥</div>
            <div class="text-2xl font-bold text-gold" x-text="formatNumber(globalStats.active_investors)"></div>
            <div class="text-xs text-ivory/50">Active Investors</div>
        </div>
        <div class="luxury-card p-4 text-center">
            <div class="text-2xl mb-2">💰</div>
            <div class="text-2xl font-bold text-gold" x-text="formatNumber(globalStats.daily_payout)"></div>
            <div class="text-xs text-ivory/50">Daily Payout (KES)</div>
        </div>
        <div class="luxury-card p-4 text-center">
            <div class="text-2xl mb-2">📊</div>
            <div class="text-2xl font-bold text-green-400" x-text="globalStats.avg_roi + '%'"></div>
            <div class="text-xs text-ivory/50">Average ROI</div>
        </div>
        <div class="luxury-card p-4 text-center">
            <div class="text-2xl mb-2">⭐</div>
            <div class="text-2xl font-bold text-gold" x-text="globalStats.rating"></div>
            <div class="text-xs text-ivory/50">Trust Score</div>
        </div>
        <div class="luxury-card p-4 text-center">
            <div class="text-2xl mb-2">🌍</div>
            <div class="text-2xl font-bold text-gold" x-text="globalStats.countries"></div>
            <div class="text-xs text-ivory/50">Countries</div>
        </div>
    </div>

    <!-- RX Machines Grid with Tabs -->
    <div class="mb-6">
        <div class="flex flex-wrap gap-2 border-b border-gold/30 pb-3">
            <button @click="activeTab = 'all'" :class="{'bg-gold/20 text-gold': activeTab === 'all'}" class="luxury-tab px-6 py-2 rounded-full transition">All Machines</button>
            <button @click="activeTab = 'low'" :class="{'bg-gold/20 text-gold': activeTab === 'low'}" class="luxury-tab px-6 py-2 rounded-full transition">Low Risk</button>
            <button @click="activeTab = 'medium'" :class="{'bg-gold/20 text-gold': activeTab === 'medium'}" class="luxury-tab px-6 py-2 rounded-full transition">Medium Risk</button>
            <button @click="activeTab = 'high'" :class="{'bg-gold/20 text-gold': activeTab === 'high'}" class="luxury-tab px-6 py-2 rounded-full transition">High Risk</button>
            <button @click="activeTab = 'vip'" :class="{'bg-gold/20 text-gold': activeTab === 'vip'}" class="luxury-tab px-6 py-2 rounded-full transition">VIP Only</button>
        </div>
    </div>

    <!-- Machines Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        <template x-for="machine in filteredMachines" :key="machine.code">
            <div class="enterprise-machine-card rounded-2xl overflow-hidden shadow-2xl transition-all duration-300 hover:scale-105">
                <!-- Card Header -->
                <div class="relative h-48 bg-cover bg-center" :style="'background-image: url(' + machine.background + ')'">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="text-3xl mb-1" x-html="machine.icon"></div>
                                <h3 class="text-xl font-bold text-white" x-text="machine.name"></h3>
                                <p class="text-xs text-gold-400" x-text="machine.risk_profile + ' Risk'"></p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-gold" x-text="'KES ' + formatNumber(machine.stats.total_invested)"></div>
                                <div class="text-xs text-ivory/50">Total Invested</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VIP Tiers Tabs -->
                <div class="p-5 bg-cosmic-deep">
                    <div class="flex gap-2 mb-4 overflow-x-auto">
                        <template x-for="vip in machine.vip_tiers" :key="vip.level">
                            <button @click="machine.activeVip = vip.level" 
                                :class="{'bg-gold text-black': machine.activeVip === vip.level, 'bg-gold/10 text-gold': machine.activeVip !== vip.level}"
                                class="px-4 py-2 rounded-full text-sm font-semibold transition">
                                <span x-text="vip.name"></span>
                                <span class="text-xs ml-1" x-text="vip.phi_power"></span>
                            </button>
                        </template>
                    </div>

                    <!-- Active VIP Details -->
                    <template x-for="vip in machine.vip_tiers" :key="vip.level">
                        <div x-show="machine.activeVip === vip.level" class="space-y-4">
                            <!-- Amount -->
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gold" x-text="'KES ' + formatNumber(vip.amount)"></div>
                                <div class="text-xs text-ivory/50">Minimum Investment</div>
                            </div>

                            <!-- Stats Grid -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-black/30 rounded-xl p-3 text-center">
                                    <div class="text-green-400 text-xl font-bold" x-text="'KES ' + formatNumber(vip.daily_profit)"></div>
                                    <div class="text-xs text-ivory/50">Daily Profit</div>
                                </div>
                                <div class="bg-black/30 rounded-xl p-3 text-center">
                                    <div class="text-gold text-xl font-bold" x-text="vip.roi + '%'"></div>
                                    <div class="text-xs text-ivory/50">ROI (14 days)</div>
                                </div>
                                <div class="bg-black/30 rounded-xl p-3 text-center">
                                    <div class="text-gold text-xl font-bold" x-text="'KES ' + formatNumber(vip.total_return)"></div>
                                    <div class="text-xs text-ivory/50">Total Return</div>
                                </div>
                                <div class="bg-black/30 rounded-xl p-3 text-center">
                                    <div class="text-green-400 text-xl font-bold" x-text="vip.apy + '%'"></div>
                                    <div class="text-xs text-ivory/50">APY (Annual)</div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-ivory/50">Funding Progress</span>
                                    <span class="text-gold" x-text="vip.progress + '%'"></span>
                                </div>
                                <div class="w-full bg-gold/20 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-gold-400 to-gold-600 h-2 rounded-full transition-all" :style="'width: ' + vip.progress + '%'"></div>
                                </div>
                            </div>

                            <!-- Features -->
                            <div class="flex flex-wrap gap-2">
                                <template x-for="feature in vip.features">
                                    <span class="text-xs px-2 py-1 bg-gold/10 rounded-full text-gold-400" x-text="feature"></span>
                                </template>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-3 mt-4">
                                <button @click="invest(machine.id, vip.level, vip.amount)" class="flex-1 btn-golden py-3">
                                    <i class="fas fa-gem mr-2"></i> Invest Now
                                </button>
                                <button @click="viewDetails(machine.code)" class="px-4 bg-transparent border border-gold rounded-xl text-gold hover:bg-gold/10 transition">
                                    <i class="fas fa-chart-line"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Card Footer -->
                <div class="p-4 border-t border-gold/20 bg-cosmic-deep/50 flex justify-between text-xs">
                    <span class="text-ivory/50"><i class="fas fa-users mr-1"></i> <span x-text="formatNumber(machine.stats.total_investors)"></span> investors</span>
                    <span class="text-ivory/50"><i class="fas fa-chart-line mr-1"></i> <span x-text="machine.stats.completion_rate + '%'"></span> completion</span>
                    <span class="text-gold-400"><i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </template>
    </div>

    <!-- Loading Skeleton -->
    <div x-show="loading" class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        <template x-for="i in 6">
            <div class="animate-pulse">
                <div class="bg-gold/10 rounded-2xl h-96"></div>
            </div>
        </template>
    </div>
</div>

<style>
.luxury-card {
    background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(212, 175, 55, 0.05) 100%);
    border: 1px solid rgba(212, 175, 55, 0.3);
    border-radius: 1rem;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}
.luxury-card:hover {
    border-color: rgba(212, 175, 55, 0.6);
    transform: translateY(-2px);
}
.luxury-tab {
    background: rgba(212, 175, 55, 0.1);
    color: #d4af37;
}
.luxury-tab:hover {
    background: rgba(212, 175, 55, 0.2);
}
.enterprise-machine-card {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border: 1px solid rgba(212, 175, 55, 0.3);
    transition: all 0.3s ease;
}
.enterprise-machine-card:hover {
    border-color: rgba(212, 175, 55, 0.6);
    box-shadow: 0 20px 40px rgba(212, 175, 55, 0.1);
}
</style>

<script>
function enterpriseRXManager() {
    return {
        loading: true,
        activeTab: 'all',
        machines: [],
        globalStats: {
            total_invested: 0,
            total_profit: 0,
            active_investors: 0,
            daily_payout: 0,
            avg_roi: 0,
            rating: 4.8,
            countries: 25
        },

        async init() {
            await this.fetchMachines();
            this.loading = false;
            setInterval(() => this.fetchMachines(), 30000);
        },

        async fetchMachines() {
            try {
                const response = await fetch('/api/v1/machines', {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem('token') }
                });
                const data = await response.json();
                if (data.success) {
                    this.machines = data.data.map(m => ({
                        ...m,
                        activeVip: 1,
                        background: `/images/machines/${m.code.toLowerCase()}.jpg`,
                        icon: this.getMachineIcon(m.code)
                    }));
                    this.calculateGlobalStats();
                }
            } catch (error) {
                console.error('Failed to fetch machines:', error);
            }
        },

        calculateGlobalStats() {
            let totalInvested = 0;
            let totalProfit = 0;
            let totalInvestors = 0;
            let dailyPayout = 0;
            let totalRoi = 0;

            this.machines.forEach(m => {
                totalInvested += m.stats.total_invested;
                totalProfit += m.stats.total_profit;
                totalInvestors += m.stats.total_investors;
                dailyPayout += m.stats.daily_payout || 0;
                totalRoi += m.stats.roi_percentage || 0;
            });

            this.globalStats = {
                total_invested: totalInvested,
                total_profit: totalProfit,
                active_investors: totalInvestors,
                daily_payout: dailyPayout,
                avg_roi: this.machines.length ? (totalRoi / this.machines.length).toFixed(1) : 0,
                rating: 4.8,
                countries: 25
            };
        },

        get filteredMachines() {
            if (this.activeTab === 'all') return this.machines;
            if (this.activeTab === 'low') return this.machines.filter(m => m.risk_profile === 'Low' || m.risk_profile === 'Low-Medium');
            if (this.activeTab === 'medium') return this.machines.filter(m => m.risk_profile === 'Medium' || m.risk_profile === 'Medium-High');
            if (this.activeTab === 'high') return this.machines.filter(m => m.risk_profile === 'High' || m.risk_profile === 'Very High');
            if (this.activeTab === 'vip') return this.machines.filter(m => m.code.includes('RX6'));
            return this.machines;
        },

        getMachineIcon(code) {
            const icons = {
                'RX1': '🪶',
                'RX2': '⭐',
                'RX3': '💎',
                'RX4': '🌙',
                'RX5': '⚛️',
                'RX6': '∞'
            };
            return icons[code] || '🔷';
        },

        async invest(machineId, vipLevel, amount) {
            if (!confirm(`✨ Invest KES ${amount.toLocaleString()} in VIP ${vipLevel}?\n\n📈 Daily profit: ${(vipLevel * 0.5 + 1.5).toFixed(2)}%\n💰 Total return: ${(vipLevel * 2 + 23)}% of investment\n⏱️ Duration: 14 days\n\nDaily profit will be credited automatically to your wallet.`)) {
                return;
            }
            
            try {
                const response = await fetch(`/api/v1/machines/${machineId}/invest`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token'),
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ vip_level: vipLevel })
                });
                
                const data = await response.json();
                if (data.success) {
                    alert('✅ Investment successful!\n\n' + data.message);
                    location.reload();
                } else {
                    alert('❌ Investment failed: ' + data.message);
                }
            } catch (err) {
                alert('❌ Error: ' + err.message);
            }
        },

        viewDetails(code) {
            window.location.href = `/machines/${code}`;
        },

        formatNumber(num) {
            if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
            if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
            return num.toLocaleString();
        }
    }
}
</script>
