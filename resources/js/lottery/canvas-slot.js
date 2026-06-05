import { svgSymbols, getSymbolDisplay } from './svg-symbols.js';

export class CanvasSlotMachine {
    constructor(canvasId, symbols, onSpinComplete, useSvg = true) {
        this.canvas = document.getElementById(canvasId);
        this.ctx = this.canvas.getContext('2d');
        this.symbols = symbols;
        this.useSvg = useSvg;
        this.reelCount = 3;
        this.reelWidth = 0;
        this.spinning = false;
        this.reelPositions = [0, 0, 0];
        this.reelSpeeds = [0, 0, 0];
        this.finalSymbolIndices = [];
        this.onSpinComplete = onSpinComplete;
        this.setupCanvas();
        window.addEventListener('resize', () => this.resize());
    }

    setupCanvas() {
        const container = this.canvas.parentElement;
        const maxWidth = Math.min(600, container.clientWidth - 40);
        this.canvas.width = maxWidth;
        this.canvas.height = maxWidth * 0.4;
        this.reelWidth = this.canvas.width / this.reelCount;
        this.drawStatic();
    }

    resize() { this.setupCanvas(); }

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
            this.ctx.lineWidth = 2;
            this.ctx.strokeRect(i * this.reelWidth, 0, this.reelWidth, this.canvas.height);
        }
    }

    drawReel(index, offset) {
        const symbolHeight = this.canvas.height / 3;
        const visibleCount = 4;
        const startIdx = Math.floor(offset / symbolHeight);
        for (let i = 0; i < visibleCount; i++) {
            const symIdx = (startIdx + i) % this.symbols.length;
            const y = i * symbolHeight - (offset % symbolHeight);
            if (y > -symbolHeight && y < this.canvas.height) {
                const centerX = index * this.reelWidth + this.reelWidth / 2;
                const centerY = y + symbolHeight / 2;
                this.drawSymbol(this.symbols[symIdx], centerX, centerY, symbolHeight * 0.6);
            }
        }
    }

    drawSymbol(symbol, x, y, size) {
        this.ctx.save();
        this.ctx.textAlign = 'center';
        this.ctx.textBaseline = 'middle';
        
        if (this.useSvg && svgSymbols[symbol.name]) {
            // Draw SVG as image
            const img = new Image();
            img.src = 'data:image/svg+xml,' + encodeURIComponent(svgSymbols[symbol.name].svg);
            img.width = size;
            img.height = size;
            this.ctx.drawImage(img, x - size/2, y - size/2, size, size);
        } else {
            // Fallback to emoji
            const display = getSymbolDisplay(symbol.name, false);
            this.ctx.font = `${size}px "Segoe UI Emoji"`;
            this.ctx.fillStyle = '#FFD700';
            this.ctx.shadowBlur = 8;
            this.ctx.shadowColor = '#FFD700';
            this.ctx.fillText(display.emoji, x, y);
        }
        this.ctx.restore();
    }

    startSpin(finalSymbolNames) {
        if (this.spinning) return;
        this.finalSymbolIndices = finalSymbolNames.map(name =>
            this.symbols.findIndex(s => s.name === name)
        );
        this.spinning = true;
        for (let i = 0; i < this.reelCount; i++) {
            this.reelSpeeds[i] = 20 + Math.random() * 30;
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
                this.reelSpeeds[i] *= 0.96;
                if (this.reelSpeeds[i] < 0.8) {
                    this.reelPositions[i] = this.finalSymbolIndices[i] * (this.canvas.height / 3);
                    this.reelSpeeds[i] = 0;
                }
            }
        }
        this.drawStatic();
        if (allStopped) {
            this.spinning = false;
            this.drawWinLines();
            if (this.onSpinComplete) this.onSpinComplete();
        } else {
            requestAnimationFrame(() => this.animate());
        }
    }

    drawWinLines() {
        this.ctx.save();
        this.ctx.strokeStyle = '#FFD700';
        this.ctx.lineWidth = 3;
        this.ctx.shadowBlur = 10;
        this.ctx.shadowColor = '#FFD700';
        this.ctx.beginPath();
        this.ctx.moveTo(0, this.canvas.height / 2);
        this.ctx.lineTo(this.canvas.width, this.canvas.height / 2);
        this.ctx.stroke();
        this.ctx.restore();
        setTimeout(() => this.drawStatic(), 500);
    }

    stopSpin() { this.spinning = false; }
}

// Simple DOM-based fallback reels (if canvas fails)
export class SimpleReels {
    constructor(reel1Id, reel2Id, reel3Id) {
        this.reel1 = document.getElementById(reel1Id);
        this.reel2 = document.getElementById(reel2Id);
        this.reel3 = document.getElementById(reel3Id);
    }
    startSpin(finalSymbolNames, symbols) {
        this.reel1.innerHTML = symbols.find(s => s.name === finalSymbolNames[0])?.icon || '🎰';
        this.reel2.innerHTML = symbols.find(s => s.name === finalSymbolNames[1])?.icon || '🎰';
        this.reel3.innerHTML = symbols.find(s => s.name === finalSymbolNames[2])?.icon || '🎰';
        // Add animation class
        [this.reel1, this.reel2, this.reel3].forEach(reel => {
            reel.classList.add('animate-spin-once');
            setTimeout(() => reel.classList.remove('animate-spin-once'), 300);
        });
    }
}

export function confetti() {
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
            x: canvas.width / 2, y: canvas.height / 2,
            vx: (Math.random() - 0.5) * 15, vy: (Math.random() - 0.5) * 15 - 8,
            life: 1, size: Math.random() * 8 + 4,
            color: `hsl(${Math.random() * 360}, 100%, 60%)`
        });
    }
    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        let any = false;
        for (let p of particles) {
            p.x += p.vx; p.y += p.vy; p.vy += 0.2; p.life -= 0.01;
            if (p.life > 0) {
                any = true;
                ctx.globalAlpha = p.life;
                ctx.fillStyle = p.color;
                ctx.fillRect(p.x, p.y, p.size, p.size);
            }
        }
        if (any) requestAnimationFrame(animate);
        else canvas.remove();
    }
    animate();
}
