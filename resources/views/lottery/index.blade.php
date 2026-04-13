@extends('layouts.app')

@section('content')
<div x-data="lotteryMachine()" x-init="init()" class="py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-5xl md:text-6xl font-bold golden-title">🌀 Cosmic Lottery</h1>
            <p class="text-gold-400 mt-2">8 Divine Symbols | 8888 Hz</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="card-golden p-4 text-center">
                <p class="text-gold-400 text-sm">Sacred Balance</p>
                <p class="text-3xl font-bold text-gold" x-text="'KES ' + formatNumber(balance)"></p>
            </div>
            <div class="card-golden p-4 text-center">
                <p class="text-gold-400 text-sm">Super Jackpot</p>
                <p class="text-3xl font-bold text-gold" x-text="'KES ' + formatNumber(jackpot)"></p>
            </div>
            <div class="card-golden p-4 text-center">
                <p class="text-gold-400 text-sm">Mini Jackpot</p>
                <p class="text-3xl font-bold text-gold">KES 5,000</p>
            </div>
            <div class="card-golden p-4 text-center">
                <p class="text-gold-400 text-sm">Daily Free Spin</p>
                <button @click="freeSpin()" :disabled="spinning || !canFreeSpin" class="btn-golden text-sm py-1 px-3">
                    <span x-text="canFreeSpin ? 'Claim Free Spin' : 'Free Spin Used'"></span>
                </button>
            </div>
        </div>

        <!-- Bet Selector -->
        <div class="card-golden p-4 mb-6">
            <div class="flex flex-wrap justify-center gap-3">
                <button @click="betAmount = 10" class="btn-golden text-sm">KES 10</button>
                <button @click="betAmount = 50" class="btn-golden text-sm">KES 50</button>
                <button @click="betAmount = 100" class="btn-golden text-sm">KES 100</button>
                <button @click="betAmount = 500" class="btn-golden text-sm">KES 500</button>
                <button @click="betAmount = 1000" class="btn-golden text-sm">KES 1000</button>
            </div>
        </div>

        <!-- Slot Machine -->
        <div class="card-golden p-8 mb-6 text-center">
            <div class="flex justify-center gap-6 mb-8">
                <div class="reel w-36 h-36 bg-cosmic-deep rounded-2xl border-4 border-gold flex items-center justify-center text-7xl" :class="{'animate-spin-once': spinning}" x-text="getSymbolEmoji(reelSymbols[0])"></div>
                <div class="reel w-36 h-36 bg-cosmic-deep rounded-2xl border-4 border-gold flex items-center justify-center text-7xl" :class="{'animate-spin-once': spinning}" x-text="getSymbolEmoji(reelSymbols[1])"></div>
                <div class="reel w-36 h-36 bg-cosmic-deep rounded-2xl border-4 border-gold flex items-center justify-center text-7xl" :class="{'animate-spin-once': spinning}" x-text="getSymbolEmoji(reelSymbols[2])"></div>
            </div>
            <button @click="spin()" :disabled="spinning" class="btn-golden text-2xl py-4 px-12">
                <span x-text="spinning ? 'SPINNING...' : 'SPIN (KES ' + betAmount + ')'"></span>
            </button>
            <div x-show="winAmount > 0" class="mt-6">
                <div class="inline-block bg-green-500/20 rounded-2xl p-4">
                    <p class="text-3xl font-bold text-green-400" x-text="'🎉 YOU WON KES ' + formatNumber(winAmount) + '! 🎉'"></p>
                </div>
            </div>
        </div>

        <!-- Recent Spins -->
        <div class="card-golden p-5">
            <h3 class="text-xl font-bold text-gold mb-4">📜 Recent Divine Spins</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-gold/30">
                        <tr><th class="px-4 py-2">Time</th><th class="px-4 py-2">Bet</th><th class="px-4 py-2">Win</th><th class="px-4 py-2">Result</th></tr>
                    </thead>
                    <tbody>
                        @forelse($history as $spin)
                        <tr>
                            <td class="px-4 py-2">{{ $spin->created_at->diffForHumans() }}</td>
                            <td class="px-4 py-2">KES {{ number_format($spin->bet_amount, 2) }}</td>
                            <td class="px-4 py-2 text-green-400">KES {{ number_format($spin->win_amount, 2) }}</td>
                            <td class="px-4 py-2">
                                <div class="flex gap-1 text-xl">
                                    @foreach($spin->result['names'] ?? [] as $name)
                                        @if($name == 'divine_sword') ⚔️
                                        @elseif($name == 'divine_bell') 🔔
                                        @elseif($name == 'golden_flower') 🌸
                                        @elseif($name == 'frequency_8888') 8888
                                        @elseif($name == 'frequency_7777') 7777
                                        @elseif($name == 'taurus') ♉
                                        @elseif($name == 'tree_of_life') 🌳
                                        @elseif($name == 'divine_star') ⭐
                                        @else ❓
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-8">No spins yet. Start your cosmic journey!</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes spinOnce { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
.animate-spin-once { animation: spinOnce 0.5s ease-out; }
</style>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1"></script>
<script>
function lotteryMachine() {
    return {
        balance: {{ $balance }},
        jackpot: {{ $game->progressive_jackpot ?? 1000 }},
        betAmount: 10,
        spinning: false,
        reelSymbols: ['divine_sword', 'divine_bell', 'golden_flower'],
        winAmount: 0,
        miniJackpot: false,
        superJackpot: false,
        freeSpinTrigger: false,
        lastMessage: '',
        canFreeSpin: {{ $canFreeSpin ? 'true' : 'false' }},
        freeSpinHours: {{ $freeSpinHours }},
        leaderboard: @json($leaderboard),

        getSymbolEmoji(symbolName) {
            const map = {
                'divine_sword': '⚔️',
                'divine_bell': '🔔',
                'golden_flower': '🌸',
                'frequency_8888': '8888',
                'frequency_7777': '7777',
                'taurus': '♉',
                'tree_of_life': '🌳',
                'divine_star': '⭐'
            };
            return map[symbolName] || '?';
        },

        randomSymbol() {
            const symbols = ['divine_sword', 'divine_bell', 'golden_flower', 'frequency_8888', 'frequency_7777', 'taurus', 'tree_of_life', 'divine_star'];
            return symbols[Math.floor(Math.random() * symbols.length)];
        },

        init() {
            this.startJackpotPolling();
        },

        startJackpotPolling() {
            setInterval(() => {
                fetch('/api/lottery/jackpot')
                    .then(res => res.json())
                    .then(data => { if (data.jackpot) this.jackpot = data.jackpot; })
                    .catch(() => {});
            }, 5000);
        },

        showConfetti() {
            canvasConfetti({ particleCount: 200, spread: 100, origin: { y: 0.6 }, colors: ['#D4AF37', '#FFD700', '#FFFFFF'] });
        },

        async spin() {
            if (this.spinning) return;
            if (this.betAmount > this.balance) {
                this.lastMessage = 'Insufficient balance.';
                return;
            }
            this.spinning = true;
            this.winAmount = 0;
            let interval = setInterval(() => {
                this.reelSymbols = [this.randomSymbol(), this.randomSymbol(), this.randomSymbol()];
            }, 80);
            try {
                const response = await fetch('{{ route("lottery.spin") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ bet: this.betAmount })
                });
                const data = await response.json();
                clearInterval(interval);
                if (data.success) {
                    this.reelSymbols = data.symbols.map(s => s.name);
                    this.winAmount = data.win_amount;
                    this.miniJackpot = data.mini_jackpot;
                    this.superJackpot = data.super_jackpot;
                    this.freeSpinTrigger = data.free_spin_trigger;
                    this.balance = data.new_balance;
                    this.jackpot = data.progressive_jackpot;
                    if (data.win_amount > 0) this.showConfetti();
                } else {
                    this.lastMessage = data.message || 'Error occurred.';
                }
            } catch (err) {
                clearInterval(interval);
                this.lastMessage = 'Network error.';
            } finally {
                this.spinning = false;
            }
        },

        async freeSpin() {
            if (this.spinning || !this.canFreeSpin) return;
            this.spinning = true;
            let interval = setInterval(() => {
                this.reelSymbols = [this.randomSymbol(), this.randomSymbol(), this.randomSymbol()];
            }, 80);
            try {
                const response = await fetch('{{ route("lottery.free-spin") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await response.json();
                clearInterval(interval);
                if (data.success) {
                    this.reelSymbols = data.symbols.map(s => s.name);
                    this.winAmount = data.win_amount;
                    this.balance = data.new_balance;
                    this.jackpot = data.progressive_jackpot;
                    this.canFreeSpin = false;
                    if (data.win_amount > 0) this.showConfetti();
                } else {
                    this.lastMessage = data.message || 'Free spin not available.';
                }
            } catch (err) {
                clearInterval(interval);
                this.lastMessage = 'Network error.';
            } finally {
                this.spinning = false;
            }
        },

        formatNumber(num) {
            if (num >= 1000000) return (num / 1000000).toFixed(2) + 'M';
            if (num >= 1000) return (num / 1000).toFixed(2) + 'K';
            return num.toLocaleString();
        }
    }
}
</script>
@endsection
