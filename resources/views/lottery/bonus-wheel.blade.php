@extends('layouts.app')

@section('content')
<div x-data="bonusWheel()" x-init="init()" class="py-12 min-h-screen bg-gradient-to-br from-[#0F172A] via-[#1E1B2E] to-[#2D1B4E]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <div class="inline-block bg-black/40 backdrop-blur rounded-2xl px-8 py-4 border border-gold/40 shadow-2xl">
                <p class="text-gold-400 text-sm uppercase tracking-wider">Bonus Wheel</p>
                <p class="text-5xl md:text-6xl font-bold text-gold shimmer-gold">50000</p>
                <p class="text-xs text-ivory/50">Instant Prizes</p>
            </div>
        </div>

        <div class="card-golden p-6 md:p-8 rounded-3xl bg-gradient-to-br from-silver-gold/10 via-violet-indigo/10 to-luminous-green/10 backdrop-blur-sm border-2 border-gold/50 shadow-2xl">
            <div class="flex flex-col md:flex-row gap-8 items-center justify-center">
                <div class="relative w-80 h-80">
                    <canvas id="wheelCanvas" width="320" height="320" class="w-full h-full drop-shadow-2xl transition-transform duration-1000 ease-out"></canvas>
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-12 h-12 bg-gold rounded-full border-4 border-white shadow-lg z-10 flex items-center justify-center text-2xl font-bold text-black pointer-events-none">🎡</div>
                </div>
                <div class="flex-1 text-center md:text-left">
                    <div class="bg-black/30 rounded-2xl p-4 mb-6">
                        <p class="text-gold-400 text-sm">Your Balance</p>
                        <p class="text-3xl font-bold text-gold" x-text="'KES ' + formatNumber(balance)"></p>
                    </div>
                    <div class="flex flex-wrap gap-4 justify-center md:justify-start">
                        <button @click="spin()" :disabled="spinning || !canSpin" class="btn-golden text-xl py-3 px-8 rounded-full shadow-lg transition-all hover:scale-105">
                            <span x-text="spinning ? 'SPINNING...' : (canSpin ? '🎡 SPIN NOW' : 'SPUN TODAY')"></span>
                        </button>
                        <button @click="demoMode = !demoMode" class="btn-outline-silver text-xl py-3 px-8 rounded-full border-2 border-gold/50 hover:bg-gold/10 transition">
                            <span x-text="demoMode ? 'LIVE MODE' : 'DEMO'"></span>
                        </button>
                    </div>
                    <div x-show="result" class="mt-6 text-xl text-gold-400 animate-pulse" x-text="result"></div>
                    <div x-show="!canSpin && !demoMode" class="mt-4 text-sm text-ivory/50">Come back tomorrow for another free spin!</div>
                </div>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-3 text-center">
            <div class="bg-black/30 rounded-xl p-2"><span class="text-gold">10 KES</span></div>
            <div class="bg-black/30 rounded-xl p-2"><span class="text-gold">20 KES</span></div>
            <div class="bg-black/30 rounded-xl p-2"><span class="text-gold">50 KES</span></div>
            <div class="bg-black/30 rounded-xl p-2"><span class="text-gold">100 KES</span></div>
            <div class="bg-black/30 rounded-xl p-2"><span class="text-gold">1 Free Spin</span></div>
            <div class="bg-black/30 rounded-xl p-2"><span class="text-gold">2 Free Spins</span></div>
            <div class="bg-black/30 rounded-xl p-2"><span class="text-gold">5 Free Spins</span></div>
            <div class="bg-black/30 rounded-xl p-2"><span class="text-gold">500 KES</span></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1"></script>
<script>
function bonusWheel() {
    return {
        balance: {{ auth()->user()->wallet->balance ?? 0 }},
        spinning: false,
        canSpin: {{ $canSpin ? 'true' : 'false' }},
        result: '',
        demoMode: false,
        segments: [],
        colors: ['#D4AF37', '#B8860B', '#F5AE23', '#CD7F32', '#FFD700', '#C5A028', '#E6B800', '#B8960C'],
        canvas: null, ctx: null, currentRotation: 0,

        async init() {
            await this.fetchSegments();
            this.drawWheel();
        },
        async fetchSegments() {
            try {
                const res = await fetch('/api/lottery/bonus-wheel/segments');
                this.segments = await res.json();
                if (!this.segments.length) this.setFallbackSegments();
                this.drawWheel();
            } catch(e) { this.setFallbackSegments(); this.drawWheel(); }
        },
        setFallbackSegments() {
            this.segments = [
                { type: 'kes', value: 10 }, { type: 'kes', value: 20 }, { type: 'kes', value: 50 },
                { type: 'kes', value: 100 }, { type: 'free_spin', value: 1 }, { type: 'free_spin', value: 2 },
                { type: 'free_spin', value: 5 }, { type: 'kes', value: 500 }
            ];
        },
        drawWheel() {
            this.canvas = document.getElementById('wheelCanvas');
            if (!this.canvas) return;
            this.ctx = this.canvas.getContext('2d');
            const w = this.canvas.width, h = this.canvas.height, cx = w/2, cy = h/2, r = w/2;
            const angleStep = (2 * Math.PI) / this.segments.length;
            this.ctx.clearRect(0, 0, w, h);
            for (let i = 0; i < this.segments.length; i++) {
                const start = i * angleStep + this.currentRotation;
                const end = start + angleStep;
                this.ctx.beginPath();
                this.ctx.fillStyle = this.colors[i % this.colors.length];
                this.ctx.moveTo(cx, cy);
                this.ctx.arc(cx, cy, r, start, end);
                this.ctx.fill();
                this.ctx.save();
                this.ctx.translate(cx, cy);
                this.ctx.rotate(start + angleStep/2);
                this.ctx.fillStyle = '#0F172A';
                this.ctx.font = 'bold 14px Arial';
                const prize = this.segments[i];
                const text = prize.type === 'kes' ? `KES ${prize.value}` : `${prize.value} FS`;
                this.ctx.fillText(text, r * 0.6, 10);
                this.ctx.restore();
            }
            this.ctx.beginPath();
            this.ctx.arc(cx, cy, 20, 0, 2*Math.PI);
            this.ctx.fillStyle = '#0F172A';
            this.ctx.fill();
            this.ctx.strokeStyle = '#D4AF37';
            this.ctx.lineWidth = 3;
            this.ctx.stroke();
        },
        animateWheelToPrize(prize) {
            const targetIndex = this.segments.findIndex(s => s.value === prize.value && s.type === prize.type);
            if (targetIndex === -1) return;
            const angleStep = (2 * Math.PI) / this.segments.length;
            const targetAngle = targetIndex * angleStep + angleStep/2;
            let delta = (targetAngle - (-Math.PI/2)) % (2*Math.PI);
            if (delta < 0) delta += 2*Math.PI;
            const fullSpins = 5;
            const finalRotation = this.currentRotation + fullSpins * 2 * Math.PI + delta;
            const duration = 2000;
            const startTime = performance.now();
            const startRotation = this.currentRotation;
            const animate = (now) => {
                const elapsed = now - startTime;
                const t = Math.min(1, elapsed / duration);
                const ease = 1 - Math.pow(1 - t, 3);
                this.currentRotation = startRotation + (finalRotation - startRotation) * ease;
                this.drawWheel();
                if (t < 1) requestAnimationFrame(animate);
                else { this.currentRotation = finalRotation % (2*Math.PI); this.drawWheel(); this.spinning = false; }
            };
            requestAnimationFrame(animate);
        },
        async spin() {
            if (this.spinning) return;
            if (!this.demoMode && !this.canSpin) { this.result = 'You have already spun today!'; return; }
            this.spinning = true;
            this.result = '';
            if (!this.demoMode) {
                try {
                    const res = await fetch('{{ route("bonus-wheel.spin") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.animateWheelToPrize(data.prize);
                        this.balance = data.new_balance || this.balance;
                        this.canSpin = false;
                        const prizeText = data.prize.type === 'kes' ? `KES ${data.prize.value}` : `${data.prize.value} Free Spin(s)`;
                        this.result = `🎉 You won ${prizeText}! 🎉`;
                        canvasConfetti({ particleCount: 200, spread: 100, origin: { y: 0.6 } });
                    } else { this.result = data.message; this.spinning = false; }
                } catch(e) { this.result = 'Network error.'; this.spinning = false; }
            } else {
                const rand = Math.floor(Math.random() * this.segments.length);
                const prize = this.segments[rand];
                this.animateWheelToPrize(prize);
                const prizeText = prize.type === 'kes' ? `KES ${prize.value}` : `${prize.value} Free Spin(s)`;
                this.result = `🎉 DEMO: You won ${prizeText}! 🎉`;
                setTimeout(() => { this.spinning = false; }, 2100);
            }
        },
        formatNumber(num) {
            if (num >= 1e6) return (num/1e6).toFixed(2)+'M';
            if (num >= 1e3) return (num/1e3).toFixed(2)+'K';
            return num.toLocaleString();
        }
    }
}
</script>
<style>
.shimmer-gold {
    background: linear-gradient(135deg, #D4AF37, #FFD700, #F5AE23);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    animation: shimmer 3s infinite;
}
@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}
</style>
@endsection
