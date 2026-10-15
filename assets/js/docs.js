/* ============================================================
   RACKSEPHNOX — Docs viewer (hash router)
   Routes:
     #/                  → docs home (index of all frameworks)
     #/f/<id>            → single framework page
   ============================================================ */

(function () {
  'use strict';

  const FW = window.RX_FRAMEWORKS;
  const META = window.RX_META;

  const $sidebar = document.getElementById('sidebar-list');
  const $content = document.getElementById('doc-content');
  const $search  = document.getElementById('sb-search');
  const $toggle  = document.getElementById('sb-toggle');
  const $side    = document.getElementById('sidebar');

  /* ---------- Sidebar ---------- */
  function buildSidebar(activeId) {
    if (!$sidebar) return;
    $sidebar.innerHTML = FW.map(f => `
      <li>
        <a href="#/f/${f.id}" data-id="${f.id}" class="${f.id === activeId ? 'active' : ''}">
          <span class="n">${String(f.num).padStart(2, '0')}</span>
          <span>${f.title.replace(/ Framework| Series| Services/g, '')}</span>
        </a>
      </li>
    `).join('');
  }

  /* ---------- Home (index) ---------- */
  function renderHome() {
    document.title = `${META.name} — Documentation`;
    $content.innerHTML = `
      <div class="doc-head">
        <div class="meta">Technical Reference · 20 Frameworks</div>
        <h1>Racksephnox Documentation</h1>
        <p class="sub">Every system. Every module. Every workflow.</p>
      </div>

      <p>Racksephnox is a unified digital wealth platform — cryptocurrency trading, investments, gaming, loans, and referrals under a single authenticated experience. Built on <strong>Laravel 13</strong>, deployed globally via <strong>Cloudflare Tunnel</strong>.</p>

      <div class="callout">
        <span class="t">Signature</span>
        ${META.sig}
      </div>

      <h2>All Frameworks</h2>
      <div class="fw-grid">
        ${FW.map(f => `
          <a class="fw-card" href="#/f/${f.id}">
            <div class="top">
              <span class="fw-num">${String(f.num).padStart(2, '0')}</span>
              <span class="fw-ico">${f.icon}</span>
            </div>
            <h3>${f.title}</h3>
            <p>${f.brief}</p>
            <span class="go">Read →</span>
          </a>
        `).join('')}
      </div>
    `;
    buildSidebar(null);
    window.scrollTo({ top: 0, behavior: 'instant' });
  }

  /* ---------- Framework page ---------- */
  function renderFramework(id) {
    const f = FW.find(x => x.id === id);
    if (!f) { renderHome(); return; }

    document.title = `${f.title} — ${META.name}`;

    const idx = FW.indexOf(f);
    const prev = FW[idx - 1];
    const next = FW[idx + 1];

    $content.innerHTML = `
      <div class="doc-head">
        <div class="meta">Framework ${String(f.num).padStart(2, '0')} / ${FW.length} &nbsp;·&nbsp; ${f.icon}</div>
        <h1>${f.title}</h1>
        <p class="sub">${f.sub}</p>
      </div>

      ${f.html}

      <div class="pager">
        ${prev ? `<a href="#/f/${prev.id}"><span class="dir">← Previous</span><span class="ttl">${prev.title}</span></a>` : '<span></span>'}
        ${next ? `<a class="next" href="#/f/${next.id}"><span class="dir">Next →</span><span class="ttl">${next.title}</span></a>` : '<span></span>'}
      </div>
    `;
    buildSidebar(f.id);
    window.scrollTo({ top: 0, behavior: 'instant' });
  }

  /* ---------- Router ---------- */
  function route() {
    const hash = location.hash.replace(/^#/, '') || '/';
    const m = hash.match(/^\/f\/([\w-]+)/);
    if (m) renderFramework(m[1]);
    else renderHome();

    // Close sidebar on mobile after nav
    if (window.innerWidth <= 900) $side?.classList.remove('open');
  }

  /* ---------- Search ---------- */
  function search(q) {
    q = q.toLowerCase().trim();
    document.querySelectorAll('#sidebar-list li').forEach(li => {
      const txt = li.textContent.toLowerCase();
      li.style.display = !q || txt.includes(q) ? '' : 'none';
    });
  }

  /* ---------- Init ---------- */
  document.addEventListener('DOMContentLoaded', () => {
    window.addEventListener('hashchange', route);

    if ($search) $search.addEventListener('input', e => search(e.target.value));
    if ($toggle) $toggle.addEventListener('click', () => $side.classList.toggle('open'));

    // Keyboard: "/" focuses search
    document.addEventListener('keydown', e => {
      if (e.key === '/' && document.activeElement.tagName !== 'INPUT') {
        e.preventDefault(); $search?.focus();
      }
    });

    route();
  });
})();
