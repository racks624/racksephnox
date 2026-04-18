@extends('layouts.app')

@section('content')
<div x-data="lotteryMachine()" x-init="init()" class="py-12 min-h-screen bg-gradient-to-br from-[#0F172A] via-[#1E1B2E] to-[#2D1B4E]">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Jackpot Display -->
        <div class="text-center mb-8">
            <div class="inline-block bg-black/40 backdrop-blur rounded-2xl px-8 py-4 border border-gold/40 shadow-2xl">
                <p class="text-gold-400 text-sm uppercase tracking-wider">Progressive Super Jackpot</p>
                <p class="text-5xl md:text-7xl font-bold text-gold shimmer-gold animate-pulse" x-text="'KES ' + formatNumber(jackpot)">KES 200,000</p>
                <div class="flex justify-center gap-4 mt-2">
                    <span class="text-xs text-ivory/50">Mini Jackpot: 5,000 KES</span>
                    <span class="text-xs text-gold-400 bg-gold/20 px-2 py-0.5 rounded-full">High volatility 116</span>
                </div>
            </div>
        </div>

        <!-- Main Game Card -->
        <div class="card-golden p-6 md:p-8 rounded-3xl bg-gradient-to-br from-silver-gold/10 via-violet-indigo/10 to-luminous-green/10 backdrop-blur-sm border-2 border-gold/50 shadow-2xl">
            <!-- Reels with Inline SVGs -->
            <div class="flex justify-center gap-4 md:gap-8 mb-8">
                <div class="reel w-28 h-28 md:w-36 md:h-36 bg-cosmic-deep rounded-2xl border-4 border-gold flex items-center justify-center shadow-2xl transition-all duration-300 hover:scale-105"
                     :class="{'animate-spin-once': spinning}">
                    <div x-show="!imageError[0]" x-html="getSymbolSVG(reelSymbols[0])" class="w-20 h-20"></div>
                    <span x-show="imageError[0]" x-text="getSymbolEmoji(reelSymbols[0])" class="text-6xl"></span>
                </div>
                <div class="reel w-28 h-28 md:w-36 md:h-36 bg-cosmic-deep rounded-2xl border-4 border-gold flex items-center justify-center shadow-2xl transition-all duration-300 hover:scale-105"
                     :class="{'animate-spin-once': spinning}">
                    <div x-show="!imageError[1]" x-html="getSymbolSVG(reelSymbols[1])" class="w-20 h-20"></div>
                    <span x-show="imageError[1]" x-text="getSymbolEmoji(reelSymbols[1])" class="text-6xl"></span>
                </div>
                <div class="reel w-28 h-28 md:w-36 md:h-36 bg-cosmic-deep rounded-2xl border-4 border-gold flex items-center justify-center shadow-2xl transition-all duration-300 hover:scale-105"
                     :class="{'animate-spin-once': spinning}">
                    <div x-show="!imageError[2]" x-html="getSymbolSVG(reelSymbols[2])" class="w-20 h-20"></div>
                    <span x-show="imageError[2]" x-text="getSymbolEmoji(reelSymbols[2])" class="text-6xl"></span>
                </div>
            </div>

            <!-- Controls -->
            <div class="flex flex-wrap justify-center gap-4 mb-6">
                <button @click="spin()" :disabled="spinning" class="btn-golden text-xl py-3 px-8 rounded-full shadow-lg transition-all hover:scale-105">
                    <span x-text="spinning ? 'SPINNING...' : '🎰 PLAY NOW'"></span>
                </button>
                <button @click="demoMode = !demoMode" class="btn-outline-silver text-xl py-3 px-8 rounded-full border-2 border-gold/50 hover:bg-gold/10 transition">
                    <span x-text="demoMode ? 'LIVE MODE' : 'DEMO'"></span>
                </button>
            </div>

            <!-- Bet Selector -->
            <div class="flex flex-wrap justify-center gap-2 mb-6">
                <button @click="betAmount = 10" class="btn-golden text-sm py-1 px-3" :class="{'bg-gold text-black': betAmount === 10}">KES 10</button>
                <button @click="betAmount = 50" class="btn-golden text-sm py-1 px-3" :class="{'bg-gold text-black': betAmount === 50}">KES 50</button>
                <button @click="betAmount = 100" class="btn-golden text-sm py-1 px-3" :class="{'bg-gold text-black': betAmount === 100}">KES 100</button>
                <button @click="betAmount = 500" class="btn-golden text-sm py-1 px-3" :class="{'bg-gold text-black': betAmount === 500}">KES 500</button>
                <button @click="betAmount = 1000" class="btn-golden text-sm py-1 px-3" :class="{'bg-gold text-black': betAmount === 1000}">KES 1000</button>
            </div>

            <!-- Win / Message Display -->
            <div x-show="winAmount > 0" class="mt-4 text-center">
                <div class="inline-block bg-gradient-to-r from-green-500/20 to-emerald-500/20 rounded-2xl p-4 border border-green-500/50 animate-bounce">
                    <p class="text-2xl md:text-3xl font-bold text-green-400" x-text="'🎉 YOU WON KES ' + formatNumber(winAmount) + '! 🎉'"></p>
                    <div x-show="superJackpot" class="text-lg text-gold-400 mt-1">🌟 SUPER JACKPOT! 🌟</div>
                    <div x-show="miniJackpot" class="text-lg text-gold-400 mt-1">🌸 MINI JACKPOT! 🌸</div>
                    <div class="flex justify-center gap-4 mt-2">
                        <button @click="shareWin()" class="text-gold-400 hover:text-gold text-sm"><i class="fab fa-twitter mr-1"></i> Share</button>
                        <button @click="muteSound = !muteSound" class="text-gold-400 hover:text-gold text-sm"><i class="fas" :class="muteSound ? 'fa-volume-mute' : 'fa-volume-up'"></i> Sound</button>
                    </div>
                </div>
            </div>
            <div x-show="lastMessage && winAmount === 0" class="mt-4 text-center text-gold-400 text-sm" x-text="lastMessage"></div>

            <!-- Provably Fair Info -->
            <div x-show="lastNonce" class="mt-6 text-xs text-center text-gold-400 border-t border-gold/20 pt-4">
                <p>Provably Fair | Nonce: <span x-text="lastNonce"></span> | Client Seed: <span x-text="lastClientSeed"></span></p>
                <p class="text-gold-500">Server Seed Hash: <span x-text="lastServerSeedHashed" class="font-mono"></span></p>
                <button @click="verifySpin()" class="text-gold-400 hover:text-gold underline text-xs">Verify this spin</button>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-3 text-center">
            <a href="{{ route('lottery.tournaments') }}" class="bg-black/30 rounded-xl p-2 hover:bg-gold/10 transition">🏆 Tournaments</a>
            <a href="{{ route('lottery.missions') }}" class="bg-black/30 rounded-xl p-2 hover:bg-gold/10 transition">🎯 Missions</a>
            <a href="{{ route('bonus-wheel.index') }}" class="bg-black/30 rounded-xl p-2 hover:bg-gold/10 transition">🎡 Bonus Wheel</a>
            <a href="{{ route('lottery.social') }}" class="bg-black/30 rounded-xl p-2 hover:bg-gold/10 transition">🌍 Live Wins</a>
        </div>
    </div>
</div>

<!-- Verification Modal -->
<div id="verifyModal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 hidden" @click.away="closeVerifyModal()">
    <div class="bg-cosmic-deep rounded-2xl p-6 max-w-md w-full border border-gold/30">
        <h3 class="text-xl font-bold text-gold mb-4">Verify Spin Fairness</h3>
        <p class="text-ivory/70 text-sm mb-2">Enter the <strong>server seed</strong> (you will receive it after the spin is settled).</p>
        <input type="text" id="serverSeedInput" placeholder="Server seed" class="input-golden w-full mb-4">
        <div class="flex justify-end gap-3"><button @click="closeVerifyModal()" class="btn-outline-silver text-sm py-1 px-3">Cancel</button><button id="verifySpinBtn" class="btn-golden text-sm py-1 px-3">Verify</button></div>
    </div>
</div>

<style>
@keyframes spinOnce{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}.animate-spin-once{animation:spinOnce 0.5s ease-out;}
.shimmer-gold{background:linear-gradient(135deg,#D4AF37,#FFD700,#F5AE23);background-clip:text;-webkit-background-clip:text;color:transparent;animation:shimmer 3s infinite;}
@keyframes shimmer{0%{background-position:-200% 0}100%{background-position:200% 0}}
</style>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1"></script>
<script>
function lotteryMachine() {
    return {
        balance: {{ auth()->user()->wallet->balance ?? 0 }},
        jackpot: {{ $game->progressive_jackpot ?? 200000 }},
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
        muteSound: localStorage.getItem('lotteryMute') === 'true',
        winAudio: null, spinAudio: null,
        lastNonce: null, lastClientSeed: null, lastServerSeedHashed: null, lastSpinId: null,
        demoMode: false,
        imageError: [false, false, false],

        getSymbolSVG(symbolName) {
            const svgs = {
                divine_sword: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#D4AF37"/><stop offset="100%" stop-color="#B8860B"/></linearGradient><filter id="glow"><feGaussianBlur stdDeviation="2" result="blur"/><feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs><path d="M50 10 L55 40 L70 45 L55 50 L50 90 L45 50 L30 45 L45 40 Z" fill="url(#grad)" filter="url(#glow)" stroke="#FFF" stroke-width="1"/><circle cx="50" cy="30" r="5" fill="#FFF" opacity="0.8"/></svg>`,
                divine_bell: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#D4AF37"/><stop offset="100%" stop-color="#B8860B"/></linearGradient><filter id="glow"><feGaussianBlur stdDeviation="2"/><feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs><path d="M30 40 Q30 20 50 20 Q70 20 70 40 L75 60 Q50 75 25 60 Z" fill="url(#grad)" filter="url(#glow)"/><rect x="45" y="60" width="10" height="15" fill="url(#grad)"/><circle cx="50" cy="75" r="5" fill="#FFF"/><path d="M35 45 L65 45" stroke="#FFF" stroke-width="2" opacity="0.5"/></svg>`,
                golden_flower: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><radialGradient id="grad" cx="50%" cy="50%" r="50%"><stop offset="0%" stop-color="#FFD700"/><stop offset="100%" stop-color="#D4AF37"/></radialGradient><filter id="glow"><feGaussianBlur stdDeviation="2"/><feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs><g transform="translate(50,50)"><path d="M0,-30 L10,-10 L30,0 L10,10 L0,30 L-10,10 L-30,0 L-10,-10 Z" fill="url(#grad)" filter="url(#glow)"/><circle cx="0" cy="0" r="8" fill="#FFF"/></g></svg>`,
                frequency_8888: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#D4AF37"/><stop offset="100%" stop-color="#B8860B"/></linearGradient><filter id="glow"><feGaussianBlur stdDeviation="2"/><feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs><text x="50" y="65" font-family="Arial" font-size="28" font-weight="bold" fill="url(#grad)" text-anchor="middle" filter="url(#glow)">8888</text><path d="M20 40 L80 40 M20 60 L80 60" stroke="url(#grad)" stroke-width="3" filter="url(#glow)" stroke-dasharray="5,5"/></svg>`,
                frequency_7777: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#D4AF37"/><stop offset="100%" stop-color="#B8860B"/></linearGradient><filter id="glow"><feGaussianBlur stdDeviation="2"/><feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs><text x="50" y="65" font-family="Arial" font-size="28" font-weight="bold" fill="url(#grad)" text-anchor="middle" filter="url(#glow)">7777</text><path d="M25 45 L75 45" stroke="url(#grad)" stroke-width="3" filter="url(#glow)"/></svg>`,
                taurus: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#D4AF37"/><stop offset="100%" stop-color="#B8860B"/></linearGradient><filter id="glow"><feGaussianBlur stdDeviation="2"/><feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs><circle cx="50" cy="40" r="15" fill="none" stroke="url(#grad)" stroke-width="4" filter="url(#glow)"/><path d="M35 55 L65 55" stroke="url(#grad)" stroke-width="4" filter="url(#glow)"/><path d="M35 55 L30 70 M65 55 L70 70" stroke="url(#grad)" stroke-width="3" fill="none"/></svg>`,
                tree_of_life: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#D4AF37"/><stop offset="100%" stop-color="#B8860B"/></linearGradient><filter id="glow"><feGaussianBlur stdDeviation="2"/><feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs><path d="M50 80 L50 40 Q50 20 35 15 Q50 25 65 15 Q50 20 50 40" fill="url(#grad)" filter="url(#glow)"/><circle cx="50" cy="85" r="5" fill="url(#grad)"/><path d="M45 50 L40 60 M55 50 L60 60" stroke="url(#grad)" stroke-width="2" fill="none"/></svg>`,
                divine_star: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#FFD700"/><stop offset="100%" stop-color="#FFA500"/></linearGradient><filter id="glow"><feGaussianBlur stdDeviation="2"/><feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs><polygon points="50,10 61,35 90,40 68,60 75,90 50,75 25,90 32,60 10,40 39,35" fill="url(#grad)" filter="url(#glow)"/><circle cx="50" cy="50" r="8" fill="#FFF" opacity="0.6"/></svg>`,
            };
            return svgs[symbolName] || svgs.divine_star;
        },
        getSymbolEmoji(symbolName) {
            const m={'divine_sword':'⚔️','divine_bell':'🔔','golden_flower':'🌸','frequency_8888':'8888','frequency_7777':'7777','taurus':'♉','tree_of_life':'🌳','divine_star':'⭐'};
            return m[symbolName]||'?';
        },
        randomSymbol() {
            const s=['divine_sword','divine_bell','golden_flower','frequency_8888','frequency_7777','taurus','tree_of_life','divine_star'];
            return s[Math.floor(Math.random()*s.length)];
        },
        init() { this.initAudio(); this.startJackpotPolling(); },
        initAudio() { this.winAudio=new Audio('/sounds/win.mp3'); this.spinAudio=new Audio('/sounds/spin.mp3'); this.winAudio.volume=0.5; this.spinAudio.volume=0.3; },
        playSound(sound) { if(this.muteSound) return; if(sound==='win') this.winAudio.play().catch(e=>console.log); if(sound==='spin') this.spinAudio.play().catch(e=>console.log); },
        startJackpotPolling() { setInterval(()=>{ fetch('/api/lottery/jackpot').then(r=>r.json()).then(d=>{ if(d.jackpot) this.jackpot=d.jackpot; }).catch(()=>{}); },5000); },
        showConfetti() { canvasConfetti({particleCount:200,spread:100,origin:{y:0.6},colors:['#D4AF37','#FFD700','#FFFFFF']}); setTimeout(()=>canvasConfetti({particleCount:100,spread:70,origin:{y:0.5}}),200); },
        shareWin() { const text=`I just won KES ${this.winAmount.toLocaleString()} on @Racksephnox Cosmic Lottery! 🎰✨ #DivineWealth`; window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}`,'_blank'); },
        verifySpin() { if(!this.lastSpinId){ alert('No spin to verify yet.'); return; } const modal=document.getElementById('verifyModal'); modal.classList.remove('hidden'); const btn=document.getElementById('verifySpinBtn'); btn.onclick=async ()=>{ const seed=document.getElementById('serverSeedInput').value; if(!seed){ alert('Please enter the server seed.'); return; } try{ const res=await fetch('/api/lottery/verify',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({spin_id:this.lastSpinId,server_seed:seed})}); const data=await res.json(); if(data.verified) alert('✅ Spin verified!'); else alert('❌ Verification failed: '+data.message); }catch(e){ alert('Error verifying spin.'); } modal.classList.add('hidden'); document.getElementById('serverSeedInput').value=''; }; },
        closeVerifyModal() { document.getElementById('verifyModal').classList.add('hidden'); document.getElementById('serverSeedInput').value=''; },
        async spin() {
            if(this.spinning) return;
            if(!this.demoMode && this.betAmount>this.balance){ this.lastMessage='Insufficient balance.'; return; }
            this.spinning=true; this.winAmount=0; this.miniJackpot=false; this.superJackpot=false; this.freeSpinTrigger=false; this.lastMessage=''; this.imageError=[false,false,false];
            this.playSound('spin');
            let interval=setInterval(()=>{ this.reelSymbols=[this.randomSymbol(),this.randomSymbol(),this.randomSymbol()]; },80);
            try{
                let url = this.demoMode ? '/api/lottery/demo-spin' : '{{ route("lottery.spin") }}';
                let body = JSON.stringify({bet:this.betAmount});
                const response=await fetch(url,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body});
                const data=await response.json();
                clearInterval(interval);
                if(data.success){
                    this.reelSymbols=data.symbols.map(s=>s.name);
                    this.winAmount=data.win_amount;
                    this.miniJackpot=data.mini_jackpot;
                    this.superJackpot=data.super_jackpot;
                    this.freeSpinTrigger=data.free_spin_trigger;
                    if(!this.demoMode){
                        this.balance=data.new_balance;
                        this.jackpot=data.progressive_jackpot;
                        this.lastNonce=data.nonce;
                        this.lastClientSeed=data.client_seed;
                        this.lastServerSeedHashed=data.server_seed_hashed;
                        this.lastSpinId=data.spin_id;
                    }
                    if(data.win_amount>0){
                        this.showConfetti();
                        this.playSound('win');
                        if(this.superJackpot) this.lastMessage='🌟 SUPER JACKPOT! 🌟';
                        else if(this.miniJackpot) this.lastMessage='🌸 MINI JACKPOT! 🌸';
                        else this.lastMessage='✨ Divine Blessing! ✨';
                    } else this.lastMessage='🌀 The reels align again. Try your luck!';
                } else this.lastMessage=data.message||'Error occurred.';
            }catch(err){ clearInterval(interval); this.lastMessage='Network error.'; }
            finally{ this.spinning=false; }
        },
        formatNumber(num){ if(num>=1e6) return (num/1e6).toFixed(2)+'M'; if(num>=1e3) return (num/1e3).toFixed(2)+'K'; return num.toLocaleString(); }
    }
}
</script>
@endsection
