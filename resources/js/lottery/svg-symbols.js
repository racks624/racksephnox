// SVG symbol definitions for the lottery slot machine
export const svgSymbols = {
    divine_sword: {
        name: 'divine_sword',
        display_name: '⚔️ Divine Sword',
        svg: '<svg viewBox="0 0 100 100" class="w-full h-full"><path d="M50,10 L50,90 M20,30 L80,30 M30,20 L70,20" stroke="#E8E8E8" stroke-width="4" fill="none"/><circle cx="50" cy="50" r="15" fill="#D4AF37" stroke="#B8860B" stroke-width="2"/></svg>',
        emoji: '⚔️'
    },
    divine_bell: {
        name: 'divine_bell',
        display_name: '🔔 Divine Bell',
        svg: '<svg viewBox="0 0 100 100"><path d="M50,20 L50,70 M35,35 L65,35 M50,70 L50,85 M35,85 L65,85" stroke="#CD7F32" stroke-width="4" fill="none"/><circle cx="50" cy="50" r="12" fill="#FFD8B0"/></svg>',
        emoji: '🔔'
    },
    golden_flower: {
        name: 'golden_flower',
        display_name: '🌸 Divine Flower',
        svg: '<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="20" fill="#FF69B4"/><path d="M50,20 L50,80 M20,50 L80,50 M28,28 L72,72 M28,72 L72,28" stroke="#FFD700" stroke-width="3"/></svg>',
        emoji: '🌸'
    },
    frequency_8888: {
        name: 'frequency_8888',
        display_name: '8888 Hz',
        svg: '<svg viewBox="0 0 100 100"><text x="50" y="55" font-size="24" text-anchor="middle" fill="#00FFFF" font-weight="bold">8888</text><path d="M20,70 L80,70" stroke="#00FFFF" stroke-width="2"/></svg>',
        emoji: '📶'
    },
    frequency_7777: {
        name: 'frequency_7777',
        display_name: '7777 Hz',
        svg: '<svg viewBox="0 0 100 100"><text x="50" y="55" font-size="24" text-anchor="middle" fill="#FF4500" font-weight="bold">7777</text><path d="M20,70 L80,70" stroke="#FF4500" stroke-width="2"/></svg>',
        emoji: '📶'
    },
    taurus: {
        name: 'taurus',
        display_name: '♉ Taurus',
        svg: '<svg viewBox="0 0 100 100"><circle cx="50" cy="55" r="22" fill="#8B4513"/><circle cx="50" cy="45" r="10" fill="#D2691E"/><path d="M30,55 Q50,75 70,55" stroke="#D2691E" stroke-width="3" fill="none"/></svg>',
        emoji: '♉'
    },
    tree_of_life: {
        name: 'tree_of_life',
        display_name: '🌳 Tree of Life',
        svg: '<svg viewBox="0 0 100 100"><path d="M50,80 L50,50 M50,50 L38,38 M50,50 L62,38 M50,50 L50,30" stroke="#32CD32" stroke-width="4" fill="none"/><circle cx="50" cy="30" r="12" fill="#228B22"/></svg>',
        emoji: '🌳'
    },
    divine_star: {
        name: 'divine_star',
        display_name: '⭐ Divine Star',
        svg: '<svg viewBox="0 0 100 100"><polygon points="50,10 60,40 90,40 65,60 75,90 50,70 25,90 35,60 10,40 40,40" fill="#FFD700" stroke="#FFA500" stroke-width="2"/></svg>',
        emoji: '⭐'
    }
};

export function getSymbolDisplay(symbolName, useSvg = true) {
    const symbol = svgSymbols[symbolName];
    if (!symbol) return { html: '🎰', emoji: '🎰' };
    if (useSvg) return { html: symbol.svg, emoji: symbol.emoji };
    return { html: symbol.emoji, emoji: symbol.emoji };
}
