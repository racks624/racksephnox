// Custom SVG symbols for the slot machine – each symbol defined as an SVG path + colors
export const symbols = [
    {
        name: 'divine_sword',
        display: '⚔️ Divine Sword',
        paths: ['M12,2 L12,22 M5,8 L19,8 M8,5 L16,5'], // simple sword shape
        colors: ['#C0C0C0', '#FFD700'],
        gradient: 'linear-gradient(135deg, #E8E8E8, #B8860B)'
    },
    {
        name: 'divine_bell',
        display: '🔔 Divine Bell',
        paths: ['M12,6 L12,18 M8,10 L16,10 M12,18 L12,22 M8,22 L16,22'],
        colors: ['#CD7F32', '#B8860B'],
        gradient: 'linear-gradient(135deg, #FFD8B0, #CD7F32)'
    },
    {
        name: 'golden_flower',
        display: '🌸 Divine Flower (Scatter)',
        paths: ['M12,4 L12,20 M4,12 L20,12 M7,7 L17,17 M7,17 L17,7'],
        colors: ['#FF69B4', '#FFD700'],
        gradient: 'radial-gradient(circle, #FFD700, #FF69B4)'
    },
    {
        name: 'frequency_8888',
        display: '8888 Hz',
        paths: ['M8,8 L16,8 M8,12 L14,12 M8,16 L12,16'],
        colors: ['#00FFFF', '#008080'],
        text: '8888'
    },
    {
        name: 'frequency_7777',
        display: '7777 Hz',
        paths: ['M10,8 L10,16 M14,8 L14,16 M8,12 L16,12'],
        colors: ['#FF4500', '#8B0000'],
        text: '7777'
    },
    {
        name: 'taurus',
        display: '♉ Taurus',
        paths: ['M12,4 C14,4 16,6 16,10 C16,14 12,20 12,20 C12,20 8,14 8,10 C8,6 10,4 12,4Z'],
        colors: ['#8B4513', '#D2691E'],
        gradient: 'linear-gradient(135deg, #D2691E, #8B4513)'
    },
    {
        name: 'tree_of_life',
        display: '🌳 Tree of Life',
        paths: ['M12,20 L12,12 M12,12 L8,8 M12,12 L16,8 M12,12 L12,4'],
        colors: ['#228B22', '#32CD32'],
        gradient: 'radial-gradient(circle, #32CD32, #006400)'
    },
    {
        name: 'divine_star',
        display: '⭐ Super Jackpot',
        paths: ['M12,2 L14,9 L22,9 L16,14 L19,22 L12,17 L5,22 L8,14 L2,9 L10,9Z'],
        colors: ['#FFD700', '#FFA500'],
        gradient: 'radial-gradient(circle, #FFD700, #FF8C00)'
    }
];

export function drawSymbol(ctx, symbol, x, y, size) {
    ctx.save();
    ctx.translate(x, y);
    if (symbol.gradient) {
        const grad = ctx.createLinearGradient(-size/2, -size/2, size/2, size/2);
        grad.addColorStop(0, symbol.colors[0]);
        grad.addColorStop(1, symbol.colors[1]);
        ctx.fillStyle = grad;
    } else {
        ctx.fillStyle = symbol.colors[0];
    }
    ctx.shadowBlur = 8;
    ctx.shadowColor = '#FFD700';
    if (symbol.text) {
        ctx.font = `bold ${size * 0.6}px "Segoe UI", "Font Awesome 6 Free"`;
        ctx.fillStyle = symbol.colors[0];
        ctx.fillText(symbol.text, -size/3, size/3);
    } else {
        // Draw SVG paths (simplified – in production you'd use actual SVG paths)
        ctx.beginPath();
        for (let path of symbol.paths) {
            const parts = path.split(' ');
            let first = true;
            for (let part of parts) {
                if (part === 'M') { ctx.moveTo(parseFloat(parts[1]), parseFloat(parts[2])); }
                else if (part === 'L') { ctx.lineTo(parseFloat(parts[1]), parseFloat(parts[2])); }
                else if (part === 'C') { /* bezier – simplified */ }
                else if (part === 'Z') { ctx.closePath(); }
            }
        }
        ctx.fill();
    }
    ctx.restore();
}
