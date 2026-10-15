/* ============================================================
   RACKSEPHNOX — Landing interactions
   ============================================================ */

(function () {
  'use strict';

  const { stats, name, tagline, sig } = window.RX_META;

  /* ---------- Hero spiral canvas ---------- */
  function spiral() {
    const c = document.getElementById('spiral');
    if (!c) return;
    const ctx = c.getContext('2d');
    let w, h, t = 0, raf;

    function resize() {
      const dpr = Math.min(window.devicePixelRatio || 1, 2);
      w = c.clientWidth; h = c.clientHeight;
      c.width = w * dpr; c.height = h * dpr;
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    }

    const PHI = 1.61803398875;

    function draw() {
      ctx.clearRect(0, 0, w, h);
      const cx = w / 2, cy = h / 2;
      const maxR = Math.min(w, h) * 0.62;
      const dots = 260;

      for (let i = 0; i < dots; i++) {
        const f = i / dots;
        const ang = f * Math.PI * 2 * 3 + t;
        const r = maxR * Math.pow(f, PHI * 0.55);
        const x = cx + Math.cos(ang) * r;
        const y = cy + Math.sin(ang) * r * 0.62;
        const a = (1 - f) * 0.55 * (0.6 + 0.4 * Math.sin(t * 2 + i * 0.1));
        const s = 0.6 + (1 - f) * 1.6;
        ctx.beginPath();
        ctx.fillStyle = `rgba(212,175,55,${a})`;
        ctx.arc(x, y, s, 0, Math.PI * 2);
        ctx.fill();
      }
      t += 0.0035;
      raf = requestAnimationFrame(draw);
    }

    resize();
    window.addEventListener('resize', resize);
    draw();

    // Pause when tab hidden
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) cancelAnimationFrame(raf);
      else draw();
    });
  }

  /* ---------- Stats render ---------- */
  function renderStats() {
    const el = document.getElementById('stats');
    if (!el) return;
    el.innerHTML = stats.map(s =>
      `<div class="stat"><div class="num">${s.num}</div><div class="lbl">${s.lbl}</div></div>`
    ).join('');
  }

  /* ---------- Principles render ---------- */
  function renderPrinciples() {
    const el = document.getElementById('principles');
    if (!el) return;
    el.innerHTML = window.RX_PRINCIPLES.map(p => `
      <div class="principle">
        <span class="ico">${p.ico}</span>
        <h3>${p.t}</h3>
        <p>${p.d}</p>
      </div>
    `).join('');
  }

  /* ---------- Framework grid render ---------- */
  function renderFrameworks() {
    const el = document.getElementById('fw-grid');
    if (!el) return;
    el.innerHTML = window.RX_FRAMEWORKS.map(f => `
      <a class="fw-card" href="docs.html#/f/${f.id}">
        <div class="top">
          <span class="fw-num">${String(f.num).padStart(2, '0')}</span>
          <span class="fw-ico">${f.icon}</span>
        </div>
        <h3>${f.title}</h3>
        <p>${f.brief}</p>
        <span class="go">Open →</span>
      </a>
    `).join('');

    // Cursor-follow glow
    el.querySelectorAll('.fw-card').forEach(card => {
      card.addEventListener('mousemove', e => {
        const r = card.getBoundingClientRect();
        card.style.setProperty('--mx', (e.clientX - r.left) + 'px');
        card.style.setProperty('--my', (e.clientY - r.top) + 'px');
      });
    });
  }

  /* ---------- Tech chips ---------- */
  function renderStack() {
    const el = document.getElementById('stack');
    if (!el) return;
    el.innerHTML = window.RX_STACK.map(s => `<span class="chip">${s}</span>`).join('');
  }

  /* ---------- Burger menu ---------- */
  function menu() {
    const btn = document.getElementById('burger');
    const links = document.getElementById('nav-links');
    if (!btn || !links) return;
    btn.addEventListener('click', () => links.classList.toggle('open'));
  }

  /* ---------- Fill brand text ---------- */
  function brand() {
    document.querySelectorAll('[data-brand]').forEach(n => n.textContent = name);
    document.querySelectorAll('[data-tagline]').forEach(n => n.textContent = tagline);
    document.querySelectorAll('[data-sig]').forEach(n => n.textContent = sig);
  }

  document.addEventListener('DOMContentLoaded', () => {
    brand();
    renderStats();
    renderPrinciples();
    renderFrameworks();
    renderStack();
    menu();
    spiral();
  });
})();
