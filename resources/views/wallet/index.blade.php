@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Wallet Card -->
            <div class="lg:col-span-2">
                <div class="card-golden p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-2xl font-bold text-gold">💎 Sacred Treasury</h1>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-1 text-sm text-gold-400 hover:text-gold">
                                <i class="fas fa-globe"></i> <span>{{ auth()->user()->preferred_currency ?? 'KES' }}</span>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-32 bg-cosmic-deep/95 backdrop-blur rounded-xl border border-gold/30 shadow-xl z-50">
                                <a href="#" onclick="setCurrency('KES')" class="block px-4 py-2 text-sm hover:bg-gold/10">KES</a>
                                <a href="#" onclick="setCurrency('USD')" class="block px-4 py-2 text-sm hover:bg-gold/10">USD</a>
                                <a href="#" onclick="setCurrency('EUR')" class="block px-4 py-2 text-sm hover:bg-gold/10">EUR</a>
                                <a href="#" onclick="setCurrency('BTC')" class="block px-4 py-2 text-sm hover:bg-gold/10">BTC</a>
                                <a href="#" onclick="setCurrency('ETH')" class="block px-4 py-2 text-sm hover:bg-gold/10">ETH</a>
                                <a href="#" onclick="setCurrency('USDT')" class="block px-4 py-2 text-sm hover:bg-gold/10">USDT</a>
                            </div>
                        </div>
                    </div>

                    <!-- Balance Display -->
                    <div class="text-center mb-8">
                        <p class="text-gold-400 text-sm">Available Balance</p>
                        <p class="text-5xl font-bold text-gold shimmer-gold" id="walletBalanceDisplay">
                            {{ auth()->user()->preferred_currency ?? 'KES' }} {{ number_format($balance, 2) }}
                        </p>
                    </div>

                    <!-- Multi‑currency balances -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-center mb-6">
                        <div class="bg-black/30 rounded-xl p-2"><p class="text-xs text-ivory/60">KES</p><p class="text-lg font-bold text-gold">{{ number_format($wallet->balance, 2) }}</p></div>
                        <div class="bg-black/30 rounded-xl p-2"><p class="text-xs text-ivory/60">USD</p><p class="text-lg font-bold text-gold">{{ number_format($wallet->balance_usd, 2) }}</p></div>
                        <div class="bg-black/30 rounded-xl p-2"><p class="text-xs text-ivory/60">EUR</p><p class="text-lg font-bold text-gold">{{ number_format($wallet->balance_eur, 2) }}</p></div>
                        <div class="bg-black/30 rounded-xl p-2"><p class="text-xs text-ivory/60">BTC</p><p class="text-lg font-bold text-gold">{{ number_format($wallet->balance_btc, 8) }}</p></div>
                        <div class="bg-black/30 rounded-xl p-2"><p class="text-xs text-ivory/60">ETH</p><p class="text-lg font-bold text-gold">{{ number_format($wallet->balance_eth, 8) }}</p></div>
                        <div class="bg-black/30 rounded-xl p-2"><p class="text-xs text-ivory/60">USDT</p><p class="text-lg font-bold text-gold">{{ number_format($wallet->balance_usdt, 2) }}</p></div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-4 justify-center">
                        <a href="{{ route('deposit.form') }}" class="btn-golden">Deposit</a>
                        <a href="{{ route('withdrawal.form') }}" class="btn-outline-silver">Withdraw</a>
                    </div>
                </div>
            </div>

            <!-- Right Column: Lottery Earnings Summary -->
            <div class="space-y-6">
                <div class="card-golden p-6">
                    <h3 class="text-xl font-bold text-gold mb-4">🎰 Lottery Earnings</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-ivory/60">Total Wagered:</span><span class="text-gold">KES {{ number_format($totalWagered, 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-ivory/60">Total Won:</span><span class="text-green-400">KES {{ number_format($totalWon, 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-ivory/60">Mini Jackpots:</span><span class="text-pink-400">{{ $miniJackpotHits }}</span></div>
                        <div class="flex justify-between"><span class="text-ivory/60">Super Jackpots:</span><span class="text-gold">{{ $superJackpotHits }}</span></div>
                        <div class="flex justify-between"><span class="text-ivory/60">Free Spins Left:</span><span class="text-gold">{{ auth()->user()->free_spins_available ?? 0 }}</span></div>
                        <div class="flex justify-between border-t border-gold/20 pt-2 mt-2"><span class="text-ivory/60">Net Profit:</span><span class="text-green-400">KES {{ number_format($totalWon - $totalWagered, 2) }}</span></div>
                    </div>
                </div>
                <div class="card-golden p-6">
                    <h3 class="text-xl font-bold text-gold mb-4">📜 Quick Actions</h3>
                    <div class="space-y-2">
                        <a href="{{ route('transactions.index') }}" class="block text-gold-400 hover:text-gold">📋 Transaction History →</a>
                        <a href="{{ route('lottery.index') }}" class="block text-gold-400 hover:text-gold">🎰 Play Lottery →</a>
                        <a href="{{ route('bonus-wheel.index') }}" class="block text-gold-400 hover:text-gold">🎡 Bonus Wheel →</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setCurrency(currency) {
    fetch('/api/wallet/currency', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ currency: currency })
    }).then(() => location.reload());
}
</script>
@endsection
