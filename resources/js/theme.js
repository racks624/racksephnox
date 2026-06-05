// Multi-theme manager
const themeManager = {
    themes: ['light', 'dark', 'cosmic', 'abundance'],
    currentTheme: localStorage.getItem('theme') || 'light',

    init() {
        this.applyTheme(this.currentTheme);
    },

    applyTheme(theme) {
        document.documentElement.classList.remove(...this.themes);
        document.documentElement.classList.add(theme);
        localStorage.setItem('theme', theme);
        this.currentTheme = theme;
    },

    toggleTheme() {
        const currentIndex = this.themes.indexOf(this.currentTheme);
        const nextIndex = (currentIndex + 1) % this.themes.length;
        this.applyTheme(this.themes[nextIndex]);
    }
};

window.themeManager = themeManager;
document.addEventListener('DOMContentLoaded', () => themeManager.init());
