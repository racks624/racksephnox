class LotterySounds {
    constructor() {
        this.enabled = localStorage.getItem('lottery_sounds') !== 'false';
    }
    playSpin() { if (this.enabled) console.log('🎵 Spin sound'); }
    playWin() { if (this.enabled) console.log('🎵 Win sound'); }
    playJackpot() { if (this.enabled) console.log('🎵 Jackpot!'); }
    toggle() {
        this.enabled = !this.enabled;
        localStorage.setItem('lottery_sounds', this.enabled);
        document.getElementById('toggleSoundBtn').innerText = this.enabled ? '🔊 Sound On' : '🔇 Sound Off';
    }
}
export const sounds = new LotterySounds();
