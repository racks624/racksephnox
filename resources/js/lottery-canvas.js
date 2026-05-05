// Canvas-based slot machine with particle effects and confetti
export class CanvasSlotMachine {
    constructor(canvasId, symbols, onSpinComplete) {
        this.canvas = document.getElementById(canvasId);
        this.ctx = this.canvas.getContext('2d');
        this.symbols = symbols;
        this.onSpinComplete = onSpinComplete;
        this.reelCount = 3;
        this.reelWidth = this.canvas.width / this.reelCount;
        this.spinning = false;
        this.reelPositions = [0, 0, 0];
        this.reelSpeeds = [0, 0, 0];
        this.finalSymbols = [];
        this.animationFrame = null;
        this.setupCanvas();
    }

    setupCanvas() {
        this.canvas.width = 600;
        this.canvas.height = 200;
        this.reelWidth = this.canvas.width / this.reelCount;
        this.drawStatic();
    }

    drawStatic() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        for (let i = 0; i < this.reelCount; i++) {
            this.ctx.save();
            this.ctx.beginPath();
            this.ctx.rect(i * this.reelWidth, 0, this.reelWidth, this.canvas.height);
            this.ctx.clip();
            this.drawReel(i, this.reelPositions[i]);
            this.ctx.restore();
            this.ctx.strokeStyle = '#D4AF37';
            this.ctx.strokeRect(i * this.reelWidth, 0, this.reelWidth, this.canvas.height);
        }
    }

    drawReel(reelIndex, offset) {
        const symbolHeight = 60;
        const visibleCount = Math.ceil(this.canvas.height / symbolHeight) + 2;
        const startIndex = Math.floor(offset / symbolHeight);
        for (let i = 0; i < visibleCount; i++) {
            const symIndex = (startIndex + i) % this.symbols.length;
            const y = (i * symbolHeight) - (offset % symbolHeight);
            if (y > -symbolHeight && y < this.canvas.height) {
                this.ctx.font = '40px "Font Awesome 6 Free"';
                this.ctx.fillStyle = '#FFD700';
                this.ctx.fillText(this.symbols[symIndex].icon || '🎰', reelIndex * this.reelWidth + this.reelWidth/2 - 20, y + 45);
            }
        }
    }

    startSpin(finalSymbols) {
        if (this.spinning) return;
        this.finalSymbols = finalSymbols;
        this.spinning = true;
        // Random starting speeds
        for (let i = 0; i < this.reelCount; i++) {
            this.reelSpeeds[i] = 30 + Math.random() * 30;
        }
        this.animate();
    }

    animate() {
        if (!this.spinning) return;
        let allStopped = true;
        for (let i = 0; i < this.reelCount; i++) {
            if (this.reelSpeeds[i] > 0) {
                allStopped = false;
                this.reelPositions[i] += this.reelSpeeds[i];
                this.reelSpeeds[i] *= 0.97; // deceleration
                if (this.reelSpeeds[i] < 0.5) {
                    // Snap to final symbol
                    const targetIndex = this.finalSymbols[i];
                    const symbolHeight = 60;
                    const targetY = targetIndex * symbolHeight;
                    this.reelPositions[i] = targetY;
                    this.reelSpeeds[i] = 0;
                }
            }
        }
        this.drawStatic();
        if (allStopped) {
            this.spinning = false;
            if (this.onSpinComplete) this.onSpinComplete();
        } else {
            requestAnimationFrame(() => this.animate());
        }
    }

    stopSpin() {
        this.spinning = false;
        if (this.animationFrame) cancelAnimationFrame(this.animationFrame);
    }

    resize(width, height) {
        this.canvas.width = width;
        this.canvas.height = height;
        this.reelWidth = width / this.reelCount;
        this.drawStatic();
    }
}

// Particle effect (mini confetti)
export function burstParticles(x, y) {
    // Simple Canvas particle system
    const canvas = document.createElement('canvas');
    canvas.style.position = 'fixed';
    canvas.style.top = '0';
    canvas.style.left = '0';
    canvas.style.pointerEvents = 'none';
    canvas.style.zIndex = '9999';
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    document.body.appendChild(canvas);
    const ctx = canvas.getContext('2d');
    let particles = [];
    for (let i = 0; i < 50; i++) {
        particles.push({
            x: x,
            y: y,
            vx: (Math.random() - 0.5) * 10,
            vy: (Math.random() - 0.5) * 10 - 5,
            life: 1,
            color: `hsl(${Math.random() * 60 + 40}, 100%, 60%)`
        });
    }
    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        let anyAlive = false;
        for (let p of particles) {
            p.x += p.vx;
            p.y += p.vy;
            p.vy += 0.3;
            p.life -= 0.02;
            if (p.life > 0) {
                anyAlive = true;
                ctx.globalAlpha = p.life;
                ctx.fillStyle = p.color;
                ctx.fillRect(p.x, p.y, 5, 5);
            }
        }
        if (anyAlive) requestAnimationFrame(animate);
        else canvas.remove();
    }
    animate();
}

// Canvas Confetti (jackpot)
export function shootConfetti() {
    const canvas = document.createElement('canvas');
    canvas.style.position = 'fixed';
    canvas.style.top = '0';
    canvas.style.left = '0';
    canvas.style.pointerEvents = 'none';
    canvas.style.zIndex = '10000';
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    document.body.appendChild(canvas);
    const ctx = canvas.getContext('2d');
    let particles = [];
    for (let i = 0; i < 200; i++) {
        particles.push({
            x: canvas.width / 2,
            y: canvas.height / 2,
            vx: (Math.random() - 0.5) * 15,
            vy: (Math.random() - 0.5) * 15 - 8,
            life: 1,
            size: Math.random() * 8 + 4,
            color: `hsl(${Math.random() * 360}, 100%, 60%)`
        });
    }
    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        let anyAlive = false;
        for (let p of particles) {
            p.x += p.vx;
            p.y += p.vy;
            p.vy += 0.2;
            p.life -= 0.01;
            if (p.life > 0) {
                anyAlive = true;
                ctx.globalAlpha = p.life;
                ctx.fillStyle = p.color;
                ctx.fillRect(p.x, p.y, p.size, p.size);
            }
        }
        if (anyAlive) requestAnimationFrame(animate);
        else canvas.remove();
    }
    animate();
}
