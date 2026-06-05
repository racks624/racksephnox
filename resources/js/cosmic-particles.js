// Cosmic Particle Generator
document.addEventListener('DOMContentLoaded', () => {
    const container = document.createElement('div');
    container.className = 'particle-background';
    document.body.appendChild(container);

    function createParticle() {
        const particle = document.createElement('div');
        particle.className = 'particle';
        const size = Math.random() * 4 + 2;
        particle.style.width = size + 'px';
        particle.style.height = size + 'px';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDuration = Math.random() * 15 + 5 + 's';
        particle.style.animationDelay = Math.random() * 5 + 's';
        container.appendChild(particle);
        setTimeout(() => particle.remove(), (Math.random() * 15 + 5) * 1000);
    }

    // Generate particles periodically
    setInterval(createParticle, 200);
});
