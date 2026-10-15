/* ============================================================
   RACKSEPHNOX — Docs Viewer (Industrial)
   Hash router, sidebar search, prev/next pager, live demo mount.
   Routes:
     #/          → docs home
     #/f/<id>    → single framework
   ============================================================ */
(function () {
  'use strict';

  const FW   = window.RX_FRAMEWORKS || [];
  const META = window.RX_META || {};

  const $sidebar = document.getElementById('sidebar-list');
  const $content = document.getElementById('doc-content');
  const $search  = document.getElementById('sb-search');
  const $toggle  = document.getElementById('sb-toggle');
  const $side    = document.getElementById('sidebar');

  /* ---------- Sidebar ---------- */
  function buildSidebar(activeId) {
    if (!$sidebar) return;
    $sidebar.innerHTML = FW.map(f => {
      const mark = f.demo ? '<span style="color:var(--gold-bright);font-size:.6rem;">●</span>' : '';
      return '<li>' +
        '<a href="#/f/' + f.id + '" data-id="' + f.id + '" class="' + (f.id === activeId ? 'active' : '') + '">' +
          '<span class="n">' + String(f.num).padStart(2,'0') + '</span>' +
          '<span>' + f.title.replace(/ Framework| Series| Services| System| Center/g, '') + '</span>' +
          mark +
        '</a>' +
      '</li>';
    }).join('');
  }

  /* ---------- Home ---------- */
  function renderHome() {
    document.title = META.name + ' — Documentation';
    $content.innerHTML =
      '<div class="doc-head">' +
        '<div class="meta">Technical Reference · ' + FW.length + ' Frameworks</div>' +
        '<h1>Racksephnox Documentation</h1>' +
        '<p class="sub">Every system. Every module. Every workflow.</p>' +
      '</div>' +

      '<p>Racksephnox is a unified digital wealth platform — cryptocurrency trading, investments, gaming, loans, and referrals under a single authenticated experience. Built on <strong>Laravel 13</strong>, deployed globally via <strong>Cloudflare Tunnel</strong>.</p>' +

      '<div class="callout">' +
        '<span class="t">Signature</span>' + META.sig +
      '</div>' +

      '<h2>All Frameworks</h2>' +
      '<div class="fw-grid">' +
        FW.map(f => {
          const badge = f.demo ? '<span class="demo-tag" style="margin-left:auto;">● Live demo</span>' : '';
          return '<a class="fw-card" href="#/f/' + f.id + '">' +
            '<div class="top"><span class="fw-num">' + String(f.num).padStart(2,'0') + '</span><span class="fw-ico">' + f.icon + '</span></div>' +
            '<h3>' + f.title + '</h3>' +
            '<p>' + f.brief + '</p>' +
            '<div style="display:flex;align-items:center;gap:8px;position:relative;z-index:2;"><span class="go">Read →</span>' + badge + '</div>' +
          '</a>';
        }).join('') +
      '</div>';

    buildSidebar(null);
    window.scrollTo({ top: 0, behavior: 'instant' });
  }

  /* ---------- Framework ---------- */
  function renderFramework(id) {
    const f = FW.find(x => x.id === id);
    if (!f) { renderHome(); return; }

    document.title = f.title + ' — ' + META.name;

    const idx  = FW.indexOf(f);
    const prev = FW[idx - 1];
    const next = FW[idx + 1];

    const demoSlot = f.demo ? '<div data-demo="' + f.demo + '"></div>' : '';

    $content.innerHTML =
      '<div class="doc-head">' +
        '<div class="meta">Framework ' + String(f.num).padStart(2,'0') + ' / ' + FW.length + ' &nbsp;·&nbsp; ' + f.icon + '</div>' +
        '<h1>' + f.title + '</h1>' +
        '<p class="sub">' + f.sub + '</p>' +
      '</div>' +

      demoSlot +

      f.html +

      '<div class="pager">' +
        (prev ? '<a href="#/f/' + prev.id + '"><span class="dir">← Previous</span><span class="ttl">' + prev.title + '</span></a>' : '<span></span>') +
        (next ? '<a class="next" href="#/f/' + next.id + '"><span class="dir">Next →</span><span class="ttl">' + next.title + '</span></a>' : '<span></span>') +
      '</div>';

    // Mount live demos
    if (window.RXModules) {
      try { window.RXModules.mount($content); } catch(e){}
    }

    buildSidebar(f.id);
    window.scrollTo({ top: 0, behavior: 'instant' });
  }

  /* ---------- Router ---------- */
  function route() {
    const hash = location.hash.replace(/^#/, '') || '/';
    const m = hash.match(/^\/f\/([\w-]+)/);
    if (m) renderFramework(m[1]);
    else   renderHome();
    if (window.innerWidth <= 900 && $side) $side.classList.remove('open');
  }

  /* ---------- Search ---------- */
  function search(q) {
    q = (q || '').toLowerCase().trim();
    document.querySelectorAll('#sidebar-list li').forEach(li => {
      li.style.display = !q || li.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  }

  /* ---------- Keyboard nav ---------- */
  function keyboard() {
    document.addEventListener('keydown', e => {
      // "/" focuses search
      if (e.key === '/' && document.activeElement.tagName !== 'INPUT') {
        e.preventDefault();
        $search && $search.focus();
        return;
      }
      // Arrow navigation when not in input
      if (document.activeElement.tagName === 'INPUT') return;
      const hash = location.hash.replace(/^#/, '');
      const m = hash.match(/^\/f\/([\w-]+)/);
      if (!m) return;
      const idx = FW.findIndex(x => x.id === m[1]);
      if (idx < 0) return;
      if (e.key === 'ArrowLeft'  && idx > 0)              location.hash = '#/f/' + FW[idx-1].id;
      if (e.key === 'ArrowRight' && idx < FW.length - 1)  location.hash = '#/f/' + FW[idx+1].id;
    });
  }

  /* ---------- Init ---------- */
  document.addEventListener('DOMContentLoaded', () => {
    window.addEventListener('hashchange', route);

    if ($search) $search.addEventListener('input', e => search(e.target.value));
    if ($toggle) $toggle.addEventListener('click', () => $side.classList.toggle('open'));

    keyboard();
    route();
  });
})();
