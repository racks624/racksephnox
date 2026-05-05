class LotterySounds {
    constructor() {
        this.audioContext = null;
        this.enabled = localStorage.getItem('lottery_sounds') !== 'false';
    }

    init() {
        if (!this.audioContext && this.enabled) {
            this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
        }
    }

    playSpin() {
        if (!this.enabled) return;
        this.init();
        if (!this.audioContext) return;
        const osc = this.audioContext.createOscillator();
        const gain = this.audioContext.createGain();
        osc.connect(gain);
        gain.connect(this.audioContext.destination);
        osc.frequency.value = 800;
        gain.gain.value = 0.1;
        osc.start();
        gain.gain.exponentialRampToValueAtTime(0.00001, this.audioContext.currentTime + 0.5);
        osc.stop(this.audioContext.currentTime + 0.5);
    }

    playWin(amount) {
        if (!this.enabled) return;
        this.init();
        if (!this.audioContext) return;
        const osc = this.audioContext.createOscillator();
        const gain = this.audioContext.createGain();
        osc.connect(gain);
        gain.connect(this.audioContext.destination);
        osc.frequency.value = 440;
        gain.gain.value = 0.2;
        osc.start();
        osc.frequency.exponentialRampToValueAtTime(880, this.audioContext.currentTime + 0.3);
        gain.gain.exponentialRampToValueAtTime(0.00001, this.audioContext.currentTime + 0.6);
        osc.stop(this.audioContext.currentTime + 0.6);
    }

    playJackpot() {
        if (!this.enabled) return;
        this.init();
        if (!this.audioContext) return;
        const now = this.audioContext.currentTime;
        const osc1 = this.audioContext.createOscillator();
        const osc2 = this.audioContext.createOscillator();
        const gain = this.audioContext.createGain();
        osc1.connect(gain);
        osc2.connect(gain);
        gain.connect(this.audioContext.destination);
        osc1.frequency.value = 523.25;
        osc2.frequency.value = 659.25;
        gain.gain.value = 0.3;
        osc1.start();
        osc2.start();
        gain.gain.exponentialRampToValueAtTime(0.00001, now + 1);
        osc1.stop(now + 1);
        osc2.stop(now + 1);
    }

    playBonusWheel() {
        if (!this.enabled) return;
        this.init();
        if (!this.audioContext) return;
        const osc = this.audioContext.createOscillator();
        const gain = this.audioContext.createGain();
        osc.connect(gain);
        gain.connect(this.audioContext.destination);
        osc.frequency.value = 300;
        gain.gain.value = 0.15;
        osc.start();
        osc.frequency.exponentialRampToValueAtTime(600, this.audioContext.currentTime + 0.2);
        gain.gain.exponentialRampToValueAtTime(0.00001, this.audioContext.currentTime + 0.5);
        osc.stop(this.audioContext.currentTime + 0.5);
    }

    toggleMute() {
        this.enabled = !this.enabled;
        localStorage.setItem('lottery_sounds', this.enabled);
        if (!this.enabled && this.audioContext) {
            this.audioContext.close();
            this.audioContext = null;
        }
    }
}
export const sounds = new LotterySounds();
