@extends('layouts.app')

@section('content')
<div x-data="lotteryManager()" x-init="init()" class="py-8">
    <div class="max-w-4xl mx-auto px-4">
        <div class="card-golden p-4 md:p-6">
            <!-- Header -->
            <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
                <h1 class="text-2xl md:text-3xl font-bold golden-title">🎰 Divine Cosmic Slots</h1>
                <button id="toggleSoundBtn" class="btn-outline-silver text-sm py-1 px-3">🔊 Sound On</button>
            </div>

            <!-- Canvas Slot Machine -->
            <div class="slot-canvas-container text-center">
                <canvas id="slotCanvas" width="500" height="160" class="mx-auto border-2 border-gold/50 rounded-xl w-full max-w-[500px]"></canvas>
            </div>

            <!-- Controls -->
            <div class="flex flex-wrap justify-center gap-2 mt-5">
                <input type="number" id="betAmount" value="{{ $game->min_bet }}" min="{{ $game->min_bet }}" max="{{ $game->max_bet }}" step="10" class="input-golden w-24 text-center text-sm py-2">
                <button id="spinBtn" class="btn-golden text-base px-6 py-2">SPIN</button>
                <button id="freeSpinBtn" class="btn-outline-silver text-sm py-2 px-3" {{ !$canFreeSpin ? 'disabled' : '' }}>🎁 Free</button>
                <select id="autoSpinCount" class="input-golden w-20 text-sm py-2">
                    <option value="0">Off</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <label class="flex items-center gap-1 text-sm"><input type="checkbox" id="fastSpinMode"> Fast</label>
                <button id="stopAutoSpin" class="btn-outline-silver text-sm py-2 px-3 hidden">Stop</button>
                <button id="buyBonusBtn" class="btn-golden text-sm py-2 px-3">💎 Buy Spins</button>
            </div>

            <!-- Balance & Jackpot -->
            <div class="grid grid-cols-2 gap-3 mt-5 text-center">
                <div class="bg-gold/10 p-2 rounded"><p class="text-xs text-ivory/60">Balance</p><p class="text-xl font-bold text-gold" id="balance">KES {{ number_format($balance, 2) }}</p></div>
                <div class="bg-gold/10 p-2 rounded"><p class="text-xs text-ivory/60">Jackpot</p><p class="text-xl font-bold text-green-400" id="jackpot">KES {{ number_format($game->progressive_jackpot ?? 1000, 2) }}</p></div>
            </div>

            <!-- Recent Wins Feed -->
            <div class="mt-5 bg-cosmic-deep/50 rounded-lg p-2 max-h-28 overflow-y-auto">
                <div id="winFeed" class="text-xs text-gold-400 space-y-0.5">
                    @foreach($history->take(5) as $spin)@if($spin->win_amount>0)<div>🎉 {{ $spin->user->name }} won KES {{ number_format($spin->win_amount,2) }}</div>@endif @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module">
    import { CanvasSlotMachine, confetti } from '/resources/js/lottery/canvas-slot.js';
    import { sounds } from '/resources/js/lottery/sounds.js';
    const symbols = @json($symbols);
    let canvasMachine = null;
    let autoSpinRemaining = 0;

    window.sounds = sounds;
    document.getElementById('toggleSoundBtn').addEventListener('click', () => sounds.toggle());

    document.addEventListener('DOMContentLoaded', () => {
        canvasMachine = new CanvasSlotMachine('slotCanvas', symbols, () => {});
        document.getElementById('spinBtn').addEventListener('click', () => spin(false));
        document.getElementById('freeSpinBtn').addEventListener('click', () => spin(true));
        document.getElementById('autoSpinCount').addEventListener('change', startAutoSpin);
        document.getElementById('stopAutoSpin').addEventListener('click', stopAutoSpin);
        document.getElementById('buyBonusBtn').addEventListener('click', buyBonus);
    });

    async function spin(isFree) {
        const bet = isFree ? 0 : parseFloat(document.getElementById('betAmount').value);
        const fastMode = document.getElementById('fastSpinMode').checked;
        sounds.playSpin();
        const url = isFree ? '{{ route("lottery.free-spin") }}' : '{{ route("lottery.spin") }}';
        const body = isFree ? {} : { bet, client_seed: Math.random().toString(36) };
        const res = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(body) });
        const data = await res.json();
        if (!data.success) { alert(data.message); return; }
        canvasMachine.startSpin(data.symbols.map(s => s.name));
        if (fastMode) setTimeout(() => canvasMachine.stopSpin(), 200);
        setTimeout(() => {
            document.getElementById('balance').innerText = 'KES ' + data.new_balance.toLocaleString();
            if (data.win_amount > 0) {
                sounds.playWin();
                if (data.mini_jackpot || data.super_jackpot) { sounds.playJackpot(); confetti(); }
                alert(`🎉 You won KES ${data.win_amount.toLocaleString()}!`);
                if (data.super_jackpot) confetti();
            }
            if (data.free_spin_trigger) alert('🎁 FREE SPINS TRIGGERED!');
            if (autoSpinRemaining > 0 && !isFree) {
                autoSpinRemaining--;
                if (autoSpinRemaining > 0) setTimeout(() => spin(false), fastMode ? 100 : 800);
                else document.getElementById('stopAutoSpin').classList.add('hidden');
            }
        }, fastMode ? 200 : 800);
    }

    function startAutoSpin() {
        const count = parseInt(document.getElementById('autoSpinCount').value);
        if (count > 0) { autoSpinRemaining = count; document.getElementById('stopAutoSpin').classList.remove('hidden'); spin(false); }
    }
    function stopAutoSpin() { autoSpinRemaining = 0; document.getElementById('stopAutoSpin').classList.add('hidden'); }
    async function buyBonus() {
        const price = {{ $game->getBonusBuyPrice() ?? 100 }};
        if (!confirm(`Buy 10 free spins for ${price} KES?`)) return;
        const res = await fetch('{{ route("lottery.buy-bonus") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ cost: price }) });
        const data = await res.json();
        alert(data.success ? '10 free spins added!' : data.message);
    }
</script>
<style>
    .slot-canvas-container { background: linear-gradient(145deg, #1a1a2e, #16213e); border-radius: 1.5rem; padding: 0.75rem; box-shadow: 0 0 20px rgba(212,175,55,0.2); }
    canvas { border-radius: 0.75rem; background: #0a0a1a; }
</style>
@endsection
