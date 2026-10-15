/* ============================================================
   RACKSEPHNOX — Interactive Modules Engine (Industrial)
   17 live demos, zero dependencies, graceful degradation.
   Mounts into any element bearing [data-demo="<id>"].
   ============================================================ */

window.RXModules = (function () {
  'use strict';

  const registry = {};

  /* ---------- shared helpers ---------- */
  const fmtKES = n => 'KES ' + Math.round(n).toLocaleString('en-KE');
  const fmtUSD = n => '$' + Math.round(n).toLocaleString('en-US');
  const fmtNum = n => Math.round(n).toLocaleString('en-US');
  const clamp  = (v, lo, hi) => Math.min(Math.max(v, lo), hi);
  const $      = (el, sel) => el.querySelector(sel);
  const $$     = (el, sel) => Array.from(el.querySelectorAll(sel));
  const shell  = (label, inner, foot) => `
    <div class="demo-shell">
      <div class="demo-head">
        <span class="demo-label">${label}</span>
      </div>
      ${inner}
      ${foot ? `<div class="demo-foot">${foot}</div>` : ''}
    </div>`;

  /* ---------- mount ---------- */
  function mount(root) {
    (root || document).querySelectorAll('[data-demo]').forEach(el => {
      const id = el.dataset.demo;
      if (!registry[id]) {
        el.innerHTML = '';
        return;
      }
      // re-mount support: fire cleanup
      if (el._cleanup) { try { el._cleanup(); } catch(e){} }
      try {
        registry[id](el);
      } catch (err) {
        el.innerHTML = `<div class="demo-shell"><div class="demo-error">Demo unavailable · ${(err.message||'error')}</div></div>`;
      }
    });
  }

  /* ============================================================
     DEMO 1 — Overview stats grid (animated counters)
     ============================================================ */
  registry['overview-stats'] = function (el) {
    const stats = window.RX_META && window.RX_META.stats || [];
    el.innerHTML = `
      <div class="demo-shell">
        <div class="demo-head">
          <span class="demo-label">Platform Scale · Live</span>
          <span class="demo-tag">${stats.length} counters</span>
        </div>
        <div class="stat-demo-grid">
          ${stats.map((s, i) => `
            <div class="stat-demo-cell">
              <div class="v" data-i="${i}">0</div>
              <div class="l">${s.lbl}</div>
            </div>
          `).join('')}
        </div>
        <div class="demo-foot">Animated on view · counts up once</div>
      </div>`;

    // Animate numbers that look like "20", "60+", "50K+"
    function parseN(str){
      const m = String(str).match(/^(\d+)(.*)$/);
      if (!m) return { n: 0, suffix: str };
      return { n: +m[1], suffix: m[2] || '' };
    }
    setTimeout(() => {
      $$(el, '[data-i]').forEach(node => {
        const i = +node.dataset.i;
        const { n, suffix } = parseN(stats[i].num);
        let start = null;
        const dur = 900;
        function step(t){
          if (!start) start = t;
          const p = Math.min((t - start) / dur, 1);
          const eased = 1 - Math.pow(1 - p, 3);
          node.textContent = Math.round(n * eased) + suffix;
          if (p < 1) requestAnimationFrame(step);
          else node.textContent = stats[i].num;
        }
        requestAnimationFrame(step);
      });
    }, 100);
  };

  /* ============================================================
     DEMO 2 — Auth simulator (password strength + rate limit)
     ============================================================ */
  registry['auth-simulator'] = function (el) {
    el.innerHTML = shell(
      'Registration Password Policy · Live Preview',
      `
      <label class="demo-field">
        Try a password
        <input type="text" id="pw-in" placeholder="Type any password…" autocomplete="off" spellcheck="false">
      </label>
      <div class="pw-meter"><div class="pw-meter-bar" id="pw-bar"></div></div>
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; position:relative; z-index:2;">
        <span class="pw-verdict" id="pw-verdict">Weak</span>
        <span class="demo-tag">bcrypt · 12 rounds</span>
      </div>
      <ul class="pw-rules" id="pw-rules">
        <li data-rule="len">12+ characters</li>
        <li data-rule="upper">Uppercase letter</li>
        <li data-rule="lower">Lowercase letter</li>
        <li data-rule="digit">Number</li>
        <li data-rule="sym">Symbol</li>
      </ul>
      `,
      'Laravel validation · unique email · referral auto-generated'
    );

    const $in = $('#pw-in', el);
    const $bar = $('#pw-bar', el);
    const $vd = $('#pw-verdict', el);
    const rules = {
      len:   v => v.length >= 12,
      upper: v => /[A-Z]/.test(v),
      lower: v => /[a-z]/.test(v),
      digit: v => /\d/.test(v),
      sym:   v => /[^A-Za-z0-9]/.test(v)
    };

    function calc(){
      const v = $in.value;
      let score = 0;
      Object.keys(rules).forEach(k => {
        const ok = rules[k](v);
        el.querySelector(`[data-rule="${k}"]`).classList.toggle('ok', ok);
        if (ok) score++;
      });
      const pct = (score / 5) * 100;
      $bar.style.width = pct + '%';
      const colors = ['#f87171','#f87171','#fbbf24','#60a5fa','#4ade80'];
      $bar.style.background = colors[score] || '#4ade80';
      const verdicts = ['Weak','Weak','Fair','Good','Strong','Strong'];
      const vd = verdicts[score];
      $vd.textContent = vd + ' · ' + score + '/5';
      $vd.className = 'pw-verdict ' + (vd.toLowerCase());
    }
    $in.addEventListener('input', calc);
    calc();
  };

  /* ============================================================
     DEMO 3 — Dashboard preview
     ============================================================ */
  registry['dashboard-preview'] = function (el) {
    const widgets = [
      { l: 'Wallet',      v: 'KES 128,540', s: '+12.4% / 30d' },
      { l: 'Invested',    v: 'KES 84,200',  s: '3 active' },
      { l: 'Profit',      v: 'KES 22,310',  s: '+8.1% / 30d' },
      { l: 'Credit Score',v: '742',         s: '+22 pts' },
      { l: 'Referrals',   v: '14',          s: 'KES 3,120 earned' },
      { l: 'Jackpot',     v: 'KES 2.4M',    s: 'Gold tier live' }
    ];
    el.innerHTML = shell(
      'Dashboard Controller · Live Aggregates',
      `<div class="widget-grid">
        ${widgets.map(w => `
          <div class="widget">
            <div class="w-l">${w.l}</div>
            <div class="w-v">${w.v}</div>
            <div class="w-s">${w.s}</div>
          </div>
        `).join('')}
      </div>`,
      'cached 30–300s · unread polls 30s · BTC 10s · jackpot 15s'
    );
  };

  /* ============================================================
     DEMO 4 — Banking flow (deposit simulation)
     ============================================================ */
  registry['banking-flow'] = function (el) {
    el.innerHTML = shell(
      'Deposit Flow · Simulated',
      `
      <div class="paybill-card">
        <div class="paybill-cell">
          <div class="l">Paybill</div>
          <div class="v">200999</div>
        </div>
        <div class="paybill-cell">
          <div class="l">Account</div>
          <div class="v">0333060036213</div>
        </div>
      </div>
      <label class="demo-field">
        Enter KES amount you sent
        <input type="number" id="bk-amt" value="5000" min="10" max="1000000" step="100">
      </label>
      <div class="demo-result">
        <div class="res-row"><span>Status</span><b id="bk-status" class="warn">awaiting code</b></div>
        <div class="res-row"><span>Amount</span><b id="bk-amt-out">KES 5,000</b></div>
        <div class="res-row"><span>Wallet after verification</span><b class="gold" id="bk-wallet">KES 33,540</b></div>
      </div>
      `,
      'All credits require admin approval · duplicate detection active'
    );
    const $amt = $('#bk-amt', el);
    const START = 28540;
    function calc(){
      let a = +$amt.value || 0;
      a = clamp(a, 0, 1000000);
      $('#bk-amt-out', el).textContent = fmtKES(a);
      $('#bk-wallet', el).textContent = fmtKES(START + a);
    }
    $amt.addEventListener('input', calc);
    calc();
  };

  /* ============================================================
     DEMO 5 — M-Pesa code validator
     ============================================================ */
  registry['mpesa-validator'] = function (el) {
    el.innerHTML = shell(
      'M-Pesa Code Validator · Live',
      `
      <label class="demo-field">
        Transaction code
        <input type="text" class="code-input" id="mp-in" maxlength="10" placeholder="SFE4A8B2C1" autocomplete="off" spellcheck="false">
      </label>
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; position:relative; z-index:2; flex-wrap:wrap; gap:8px;">
        <span class="valid-badge" id="mp-badge">Awaiting input</span>
        <span class="demo-tag">10 chars · starts with letter</span>
      </div>
      <div class="demo-result" style="margin-top:12px;">
        <div class="res-row"><span>Format</span><b id="mp-fmt">—</b></div>
        <div class="res-row"><span>Length</span><b id="mp-len">0 / 10</b></div>
        <div class="res-row"><span>Amount cap</span><b>KES 1,000,000</b></div>
      </div>
      `,
      'Regex · duplicate check · statement auto-match · confidence 100/90/60'
    );
    const RE = /^[A-Z][A-Z0-9]{9}$/;
    const $in = $('#mp-in', el);
    const $bd = $('#mp-badge', el);
    const $fmt = $('#mp-fmt', el);
    const $len = $('#mp-len', el);

    function calc(){
      const v = ($in.value || '').toUpperCase().trim();
      $in.value = v;
      $len.textContent = v.length + ' / 10';
      if (!v) {
        $bd.className = 'valid-badge';
        $bd.textContent = 'Awaiting input';
        $fmt.textContent = '—';
        return;
      }
      const ok = RE.test(v);
      $bd.className = 'valid-badge ' + (ok ? 'ok' : 'err');
      $bd.textContent = ok ? '✓ Valid format' : '✗ Invalid';
      $fmt.textContent = ok ? 'Passes regex' : 'Check prefix & length';
    }
    $in.addEventListener('input', calc);
    calc();
  };

  /* ============================================================
     DEMO 6 — Crypto ticker (live BTC)
     ============================================================ */
  registry['crypto-ticker'] = function (el) {
    el.innerHTML = `
      <div class="demo-shell">
        <div class="demo-head">
          <span class="demo-label">Live · BTC/USDT</span>
          <span class="demo-pulse" title="streaming"></span>
        </div>
        <div class="demo-btc">
          <div class="btc-price" id="btc-p">—</div>
          <div class="btc-change" id="btc-c">loading…</div>
        </div>
        <div class="demo-result">
          <div class="res-row"><span>24h high</span><b id="btc-hi">—</b></div>
          <div class="res-row"><span>24h low</span><b id="btc-lo">—</b></div>
          <div class="res-row"><span>Market cap</span><b id="btc-mc">—</b></div>
        </div>
        <div class="demo-foot">Auto-refresh 15s · CoinGecko public API · cached 60s</div>
      </div>`;

    const $p  = $('#btc-p', el);
    const $c  = $('#btc-c', el);
    const $hi = $('#btc-hi', el);
    const $lo = $('#btc-lo', el);
    const $mc = $('#btc-mc', el);

    const CACHE_KEY = 'rx_btc_price_v1';
    const CACHE_TTL = 60 * 1000;

    function cacheGet(){
      try {
        const raw = localStorage.getItem(CACHE_KEY);
        if (!raw) return null;
        const j = JSON.parse(raw);
        if (Date.now() - j.t > CACHE_TTL) return null;
        return j.d;
      } catch(e){ return null; }
    }
    function cacheSet(d){
      try { localStorage.setItem(CACHE_KEY, JSON.stringify({ t: Date.now(), d })); } catch(e){}
    }

    function render(d){
      $p.textContent = fmtUSD(d.price);
      const ch = d.change;
      $c.textContent = (ch >= 0 ? '▲ +' : '▼ ') + ch.toFixed(2) + '% · 24h';
      $c.style.color = ch >= 0 ? '#4ade80' : '#f87171';
      $hi.textContent = fmtUSD(d.high);
      $lo.textContent = fmtUSD(d.low);
      $mc.textContent = '$' + (d.mcap / 1e9).toFixed(2) + 'B';
    }

    async function tick(){
      const cached = cacheGet();
      if (cached) { render(cached); return; }
      try {
        const r = await fetch('https://api.coingecko.com/api/v3/coins/bitcoin?localization=false&tickers=false&community_data=false&developer_data=false&sparkline=false');
        if (!r.ok) throw new Error('HTTP ' + r.status);
        const j = await r.json();
        const d = {
          price:  j.market_data.current_price.usd,
          change: j.market_data.price_change_percentage_24h,
          high:   j.market_data.high_24h.usd,
          low:    j.market_data.low_24h.usd,
          mcap:   j.market_data.market_cap.usd
        };
        cacheSet(d);
        render(d);
      } catch (e) {
        if (!cached) {
          $p.textContent = 'offline';
          $c.textContent = 'feed unreachable · check network';
          $c.style.color = '#fbbf24';
        }
      }
    }

    tick();
    const iv = setInterval(tick, 15000);
    el._cleanup = () => clearInterval(iv);
  };

  /* ============================================================
     DEMO 7 — Forex converter
     ============================================================ */
  registry['forex-converter'] = function (el) {
    const pairs = [
      { id:'EURUSD', label:'EUR / USD', rate: 1.0842, pip: 0.0001 },
      { id:'GBPUSD', label:'GBP / USD', rate: 1.2634, pip: 0.0001 },
      { id:'USDJPY', label:'USD / JPY', rate: 149.82, pip: 0.01   },
      { id:'USDKES', label:'USD / KES', rate: 128.45, pip: 0.01   }
    ];
    let sel = 0;

    el.innerHTML = shell(
      'Forex Console · Live Rate Preview',
      `
      <div class="fx-pairs">
        ${pairs.map((p, i) => `<button class="fx-pair ${i===0?'on':''}" data-i="${i}">${p.label}</button>`).join('')}
      </div>
      <label class="demo-field">
        Amount to convert
        <input type="number" id="fx-amt" value="1000" min="0" step="100">
      </label>
      <div class="demo-result">
        <div class="res-row"><span>Reference rate</span><b id="fx-rate">—</b></div>
        <div class="res-row"><span>Spread applied</span><b id="fx-spread">—</b></div>
        <div class="res-row"><span>Effective rate</span><b class="gold" id="fx-eff">—</b></div>
        <div class="res-row"><span>Converted</span><b id="fx-out">—</b></div>
      </div>
      `,
      'Illustrative reference · live spread determined by liquidity tier'
    );

    const $amt = $('#fx-amt', el);
    function calc(){
      const p = pairs[sel];
      const a = Math.max(+$amt.value || 0, 0);
      const spreadBps = 15;                    // 15 bps = 0.15%
      const eff = p.rate * (1 - spreadBps/10000);
      $('#fx-rate', el).textContent  = p.rate.toFixed(p.pip === 0.01 ? 3 : 4);
      $('#fx-spread', el).textContent = spreadBps + ' bps';
      $('#fx-eff', el).textContent   = eff.toFixed(p.pip === 0.01 ? 3 : 4);
      $('#fx-out', el).textContent   = (a * eff).toFixed(2) + ' ' + p.label.split('/')[1].trim();
    }
    $$('.fx-pair', el).forEach(b => b.addEventListener('click', () => {
      $$('.fx-pair', el).forEach(x => x.classList.remove('on'));
      b.classList.add('on');
      sel = +b.dataset.i;
      calc();
    }));
    $amt.addEventListener('input', calc);
    calc();
  };

  /* ============================================================
     DEMO 8 — Investment ROI calculator
     ============================================================ */
  registry['investment-roi'] = function (el) {
    const plans = [
      { n: 'Starter', min: 1000,   max: 50000,   rate: 0.0150, days: 30 },
      { n: 'Growth',  min: 10000,  max: 250000,  rate: 0.0225, days: 30 },
      { n: 'Premium', min: 50000,  max: 1000000, rate: 0.0300, days: 45 },
      { n: 'Divine',  min: 100000, max: 5000000, rate: 0.0628, days: 14 }
    ];

    el.innerHTML = shell(
      'Investment ROI Calculator',
      `
      <div class="plan-row">
        ${plans.map((p, i) => `<button class="plan-btn ${i===0?'on':''}" data-i="${i}">${p.n}</button>`).join('')}
      </div>
      <label class="demo-field">
        Amount (KES)
        <input type="number" id="i-amt" value="50000" step="1000" min="0">
      </label>
      <div class="demo-result">
        <div class="res-row"><span>Daily interest</span><b id="i-daily">—</b></div>
        <div class="res-row"><span>Duration</span><b id="i-days">—</b></div>
        <div class="res-row"><span>Total profit</span><b id="i-profit" class="gold">—</b></div>
        <div class="res-row"><span>At maturity</span><b id="i-total">—</b></div>
        <div class="res-row"><span>ROI</span><b id="i-roi">—</b></div>
      </div>
      `,
      'Auto-credit every 24h · withdraw full returns at maturity'
    );

    let sel = 0;
    const $amt = $('#i-amt', el);
    function calc(){
      const p = plans[sel];
      let a = Math.max(+$amt.value || 0, 0);
      if (a && a < p.min) a = p.min;
      if (a && a > p.max) a = p.max;
      const daily  = a * p.rate;
      const profit = daily * p.days;
      const total  = a + profit;
      const roi    = a ? (profit / a) * 100 : 0;
      $('#i-daily', el).textContent = fmtKES(daily);
      $('#i-days', el).textContent  = p.days + ' days';
      $('#i-profit', el).textContent = fmtKES(profit);
      $('#i-total', el).textContent = fmtKES(total);
      $('#i-roi', el).textContent   = roi.toFixed(1) + '%';
    }
    $$('.plan-btn', el).forEach(b => b.addEventListener('click', () => {
      $$('.plan-btn', el).forEach(x => x.classList.remove('on'));
      b.classList.add('on');
      sel = +b.dataset.i;
      calc();
    }));
    $amt.addEventListener('input', calc);
    calc();
  };

  /* ============================================================
     DEMO 9 — RX Machine φ / λ calculator
     ============================================================ */
  registry['machine-calculator'] = function (el) {
    const PHI = 1.61803398875, LAMBDA = 1.27201964951;
    const machines = [
      { n: 'RX0 Origin',   min: 1000,   max: 25000,   growth: 0.25 },
      { n: 'RX1 Aurora',   min: 2000,   max: 50000,   growth: 0.30 },
      { n: 'RX2 Nova',     min: 5000,   max: 100000,  growth: 0.38 },
      { n: 'RX3 Prism',    min: 10000,  max: 250000,  growth: 0.45 },
      { n: 'RX4 Eclipse',  min: 25000,  max: 500000,  growth: 0.55 },
      { n: 'RX5 Quantum',  min: 50000,  max: 1000000, growth: 0.70 },
      { n: 'RX6 Infinity', min: 100000, max: 2000000, growth: 0.88 }
    ];

    el.innerHTML = shell(
      'RX Machine Return Calculator',
      `
      <div style="display:flex; justify-content:flex-end; margin-bottom:8px; position:relative; z-index:2;">
        <span class="demo-tag">φ = ${PHI.toFixed(6)} · λ = ${LAMBDA.toFixed(6)}</span>
      </div>
      <div class="demo-grid">
        <label>Machine
          <select id="m-sel">${machines.map((m,i)=>`<option value="${i}">${m.n}</option>`).join('')}</select>
        </label>
        <label>VIP Tier
          <select id="m-vip">
            <option value="1">VIP 1 · ×1</option>
            <option value="2">VIP 2 · ×λ</option>
            <option value="3">VIP 3 · ×φ</option>
          </select>
        </label>
        <label>Amount (KES)
          <input type="number" id="m-amt" value="10000" min="0" step="500">
        </label>
      </div>
      <div class="demo-result">
        <div class="res-row"><span>Effective growth</span><b id="m-rate">—</b></div>
        <div class="res-row"><span>Daily accrual</span><b id="m-daily">—</b></div>
        <div class="res-row"><span>14-day profit</span><b class="gold" id="m-profit">—</b></div>
        <div class="res-row"><span>At maturity</span><b id="m-mature">—</b></div>
        <div class="res-row"><span>Referral bonus (5%)</span><b id="m-ref">—</b></div>
      </div>
      `,
      'Early withdrawal penalty · 20% · cycle · 14 days'
    );

    const $sel = $('#m-sel', el), $vip = $('#m-vip', el), $amt = $('#m-amt', el);
    function calc(){
      const m = machines[+$sel.value];
      const v = +$vip.value;
      let a = Math.max(+$amt.value || 0, 0);
      if (a && a < m.min) a = m.min;
      if (a && a > m.max) a = m.max;
      let rate = m.growth;
      if (v === 2) rate *= LAMBDA;
      if (v === 3) rate *= PHI;
      const profit = a * rate;
      const daily  = profit / 14;
      const mature = a + profit;
      const ref    = a * 0.05;
      $('#m-rate', el).textContent   = (rate * 100).toFixed(2) + '%';
      $('#m-daily', el).textContent  = fmtKES(daily);
      $('#m-profit', el).textContent = fmtKES(profit);
      $('#m-mature', el).textContent = fmtKES(mature);
      $('#m-ref', el).textContent    = fmtKES(ref);
    }
    $sel.addEventListener('change', calc);
    $vip.addEventListener('change', calc);
    $amt.addEventListener('input', calc);
    calc();
  };

  /* ============================================================
     DEMO 10 — BTC appreciation simulator
     ============================================================ */
  registry['btc-appreciation'] = function (el) {
    el.innerHTML = shell(
      'BTC Appreciation Simulator',
      `
      <div class="demo-grid">
        <label>Entry price (USD)
          <input type="number" id="b-entry" value="60000" step="1000" min="0">
        </label>
        <label>Current price (USD)
          <input type="number" id="b-now" value="92000" step="1000" min="0">
        </label>
        <label>Invested (KES)
          <input type="number" id="b-inv" value="50000" step="1000" min="0">
        </label>
      </div>
      <div class="demo-result">
        <div class="res-row"><span>Appreciation</span><b id="b-app">—</b></div>
        <div class="res-row"><span>Machine bonus</span><b id="b-bonus">—</b></div>
        <div class="res-row"><span>Total return</span><b class="gold" id="b-total">—</b></div>
        <div class="res-row"><span>Auto-compound projection</span><b id="b-comp">—</b></div>
      </div>
      `,
      '14-day cycle · uses trading_candles · auto-compound optional'
    );

    function calc(){
      const e = +$('#b-entry', el).value || 0;
      const n = +$('#b-now', el).value || 0;
      const inv = +$('#b-inv', el).value || 0;
      const appreciation = e ? ((n - e) / e) : 0;
      const machineBonus = 0.15;                        // illustrative
      const total = inv * (1 + appreciation + machineBonus);
      const comp  = total * Math.pow(1 + appreciation + machineBonus, 12);
      $('#b-app', el).textContent   = (appreciation*100).toFixed(2) + '%';
      $('#b-app', el).className     = appreciation >= 0 ? 'ok' : 'err';
      $('#b-bonus', el).textContent = '+' + (machineBonus*100).toFixed(0) + '%';
      $('#b-total', el).textContent = fmtKES(total);
      $('#b-comp', el).textContent  = fmtKES(comp);
    }
    $$('#b-entry, #b-now, #b-inv', el).forEach(i => i.addEventListener('input', calc));
    calc();
  };

  /* ============================================================
     DEMO 11 — Lottery jackpot (4-tier, animated)
     ============================================================ */
  registry['lottery-jackpot'] = function (el) {
    const tiers = [
      { n: 'Bronze', hz: '528 Hz', seed: 10000,    ceiling: 500000,   color:'#b87333' },
      { n: 'Silver', hz: '639 Hz', seed: 100000,   ceiling: 5000000,  color:'#c0c0c0' },
      { n: 'Gold',   hz: '741 Hz', seed: 1000000,  ceiling: 50000000, color:'#d4af37' },
      { n: 'Cosmic', hz: '963 Hz', seed: 10000000, ceiling: 100000000,color:'#9d7bff' }
    ];

    el.innerHTML = `
      <div class="demo-shell">
        <div class="demo-head">
          <span class="demo-label">Progressive Jackpot Pools</span>
          <span class="demo-pulse"></span>
        </div>
        <div class="jackpot-grid">
          ${tiers.map((t,i) => `
            <div class="jackpot-tier" style="border-top:3px solid ${t.color}">
              <div class="h">${t.n}</div>
              <div class="hz">${t.hz}</div>
              <div class="v" id="jp-${i}">KES 0</div>
              <div class="c">ceiling · ${fmtNum(t.ceiling)}</div>
            </div>
          `).join('')}
        </div>
        <div class="demo-foot">Pools grow every 15s · provably-fair HMAC-SHA256</div>
      </div>`;

    const vals = tiers.map(t => t.seed * (1 + Math.random() * 0.3));
    function tick(){
      vals.forEach((v, i) => {
        vals[i] = Math.min(vals[i] * (1 + 0.0006 + Math.random() * 0.0008), tiers[i].ceiling);
        $('#jp-' + i, el).textContent = fmtKES(vals[i]);
      });
    }
    tick();
    const iv = setInterval(tick, 1500);
    el._cleanup = () => clearInterval(iv);
  };

  /* ============================================================
     DEMO 12 — Loans credit score simulator
     ============================================================ */
  registry['loans-credit'] = function (el) {
    el.innerHTML = shell(
      'Credit Score Simulator',
      `
      <div class="score-display">
        <div class="v" id="cs-v">500</div>
        <div class="l">Current score · range 300–850</div>
      </div>
      <div class="score-track"><div class="score-marker" id="cs-m" style="left:36%"></div></div>
      <div class="score-btns">
        <button data-act="ontime">+ On-time payment</button>
        <button data-act="late">− Late payment</button>
        <button data-act="full">+ Full repayment</button>
        <button data-act="default">− Default</button>
        <button data-act="reset">↻ Reset</button>
      </div>
      <div class="demo-result" style="margin-top:16px;">
        <div class="res-row"><span>Events applied</span><b id="cs-n">0</b></div>
        <div class="res-row"><span>Band</span><b id="cs-band">Fair</b></div>
      </div>
      `,
      'Base 500 · +5 on-time · −10 late · +200 full · −150 default'
    );

    let score = 500, events = 0;
    function band(s){
      if (s >= 750) return 'Excellent';
      if (s >= 680) return 'Good';
      if (s >= 580) return 'Fair';
      if (s >= 500) return 'Poor';
      return 'Very Poor';
    }
    function update(){
      score = clamp(score, 300, 850);
      $('#cs-v', el).textContent = score;
      const pct = ((score - 300) / 550) * 100;
      $('#cs-m', el).style.left = pct + '%';
      $('#cs-n', el).textContent = events;
      $('#cs-band', el).textContent = band(score);
    }
    $$('button[data-act]', el).forEach(b => b.addEventListener('click', () => {
      const a = b.dataset.act;
      if (a === 'ontime')  { score += 5;   events++; }
      if (a === 'late')    { score -= 10;  events++; }
      if (a === 'full')    { score += 200; events++; }
      if (a === 'default') { score -= 150; events++; }
      if (a === 'reset')   { score = 500;  events = 0; }
      update();
    }));
    update();
  };

  /* ============================================================
     DEMO 13 — Referral link generator
     ============================================================ */
  registry['referral-link'] = function (el) {
    const code = 'RX' + Math.random().toString(36).slice(2, 8).toUpperCase();
    const baseUrl = (window.RX_META && window.RX_META.cname)
      ? 'https://' + window.RX_META.cname
      : location.origin;
    const url = baseUrl + '/register?ref=' + code;

    el.innerHTML = shell(
      'Referral Link Generator',
      `
      <div class="ref-link">
        <input type="text" id="rf-url" value="${url}" readonly>
        <button class="ref-copy" id="rf-copy">Copy</button>
      </div>
      <div class="demo-result">
        <div class="res-row"><span>Your code</span><b class="gold">${code}</b></div>
        <div class="res-row"><span>Commission</span><b>5% of referee's first investment</b></div>
        <div class="res-row"><span>Projected (20 referrals × KES 10K)</span><b id="rf-proj">${fmtKES(20 * 10000 * 0.05)}</b></div>
      </div>
      `,
      'Wallet credited instantly · counts tracked on dashboard'
    );

    $('#rf-copy', el).addEventListener('click', async () => {
      const input = $('#rf-url', el);
      try {
        await navigator.clipboard.writeText(input.value);
        const btn = $('#rf-copy', el);
        const old = btn.textContent;
        btn.textContent = '✓ Copied';
        setTimeout(() => btn.textContent = old, 1400);
      } catch(e){
        input.select();
        document.execCommand && document.execCommand('copy');
      }
    });
  };

  /* ============================================================
     DEMO 14 — Notifications preview (stack simulation)
     ============================================================ */
  registry['notifications-preview'] = function (el) {
    const events = [
      { ic:'💰', t:'Deposit confirmed',       m:'KES 5,000 credited · SFE4A8B2C1',    c:'ok',   ago:'just now' },
      { ic:'🎉', t:'Jackpot won · Silver',    m:'KES 320,000 paid to wallet',         c:'ok',   ago:'3m ago' },
      { ic:'🏦', t:'Withdrawal processed',    m:'KES 12,000 sent to M-Pesa',          c:'ok',   ago:'1h ago' },
      { ic:'📈', t:'Investment matured',      m:'Divine plan · KES 500,000 released', c:'ok',   ago:'4h ago' },
      { ic:'⏳', t:'Loan repayment due',       m:'Golden Personal · KES 4,200',        c:'warn', ago:'1d ago' },
      { ic:'🪪', t:'KYC upgraded · Enhanced', m:'New limit unlocked',                 c:'ok',   ago:'2d ago' },
    ];

    el.innerHTML = shell(
      'In-App Notification Stack · Preview',
      `<div class="notif-stack" id="nf-stack"></div>`,
      'Unread polled every 30s · email + SMS opt-out per channel'
    );

    const $s = $('#nf-stack', el);
    let i = 0;
    function push(){
      const e = events[i % events.length];
      const node = document.createElement('div');
      node.className = 'notif ' + e.c;
      node.innerHTML = `
        <span class="ic">${e.ic}</span>
        <div class="body">
          <p class="t">${e.t}</p>
          <p class="m">${e.m}</p>
        </div>
        <span class="time">${e.ago}</span>`;
      $s.prepend(node);
      while ($s.children.length > 4) $s.removeChild($s.lastChild);
      i++;
    }
    push(); push(); push();
    const iv = setInterval(push, 3800);
    el._cleanup = () => clearInterval(iv);
  };

  /* ============================================================
     DEMO 15 — KYC tiers visualizer
     ============================================================ */
  registry['kyc-tiers'] = function (el) {
    const tiers = [
      { n:'Tier 1', t:'Basic',    b:'Email + phone',       limit:'KES 50K / month' },
      { n:'Tier 2', t:'Standard', b:'National ID + selfie',limit:'KES 500K / month' },
      { n:'Tier 3', t:'Enhanced', b:'Address + statement', limit:'KES 5M / month' },
      { n:'Tier 4', t:'VIP',      b:'Full KYC + funds',    limit:'Unlimited' }
    ];

    el.innerHTML = shell(
      'KYC Progressive Unlock',
      `
      <div class="kyc-track" id="kyc-track">
        ${tiers.map((t, i) => `
          <div class="kyc-tier ${i===0?'on':''}" data-i="${i}">
            <div class="n">${t.n}</div>
            <div class="t">${t.t}</div>
            <div class="badge" data-badge>${i===0?'Active':'Locked'}</div>
          </div>
        `).join('')}
      </div>
      <div class="demo-result" style="margin-top:14px;">
        <div class="res-row"><span>Requirements</span><b id="k-req">—</b></div>
        <div class="res-row"><span>Monthly limit</span><b class="gold" id="k-lim">—</b></div>
      </div>
      `,
      'Admin reviews in /admin/kyc · approval → kyc_status = verified'
    );

    function show(i){
      $$('.kyc-tier', el).forEach(t => t.classList.remove('on'));
      $('[data-i="' + i + '"]', el).classList.add('on');
      $('#k-req', el).textContent = tiers[i].b;
      $('#k-lim', el).textContent = tiers[i].limit;
    }
    $$('.kyc-tier', el).forEach(t => t.addEventListener('click', () => show(+t.dataset.i)));
    show(0);
  };

  /* ============================================================
     DEMO 16 — Deployment pipeline (animated)
     ============================================================ */
  registry['deploy-pipeline'] = function (el) {
    const stages = [
      { ico:'📝', l:'Commit', s:'push → main' },
      { ico:'🧪', l:'Test',   s:'phpunit' },
      { ico:'🐳', l:'Build',  s:'docker' },
      { ico:'📦', l:'Push',   s:'ghcr' },
      { ico:'🌐', l:'Deploy', s:'pages' },
      { ico:'✅', l:'Live',   s:'edge' }
    ];

    el.innerHTML = `
      <div class="demo-shell">
        <div class="demo-head">
          <span class="demo-label">CI/CD Pipeline · Animated</span>
          <span class="demo-pulse"></span>
        </div>
        <div class="pipeline" id="dp">
          ${stages.map((s,i) => `
            <div class="pipe-stage" data-i="${i}">
              <span class="ico">${s.ico}</span>
              <div class="l">${s.l}</div>
              <div class="s">${s.s}</div>
            </div>
          `).join('')}
        </div>
        <div class="demo-foot">GitHub Actions · parallel jobs · cancel-in-progress</div>
      </div>`;

    let step = 0;
    function tick(){
      $$('.pipe-stage', el).forEach(s => { s.classList.remove('on'); });
      const node = $('.pipe-stage[data-i="' + step + '"]', el);
      if (node) node.classList.add('on');
      if (step > 0) {
        const prev = $('.pipe-stage[data-i="' + (step - 1) + '"]', el);
        if (prev) prev.classList.add('ok');
      }
      step = (step + 1) % (stages.length + 1);
      if (step === 0) {
        $$('.pipe-stage', el).forEach(s => s.classList.remove('ok'));
      }
    }
    tick();
    const iv = setInterval(tick, 900);
    el._cleanup = () => clearInterval(iv);
  };

  /* ============================================================
     DEMO 17 — Security layers (interactive disclosure)
     ============================================================ */
  registry['security-layers'] = function (el) {
    const layers = [
      { ico:'🖥️', t:'Application Security',
        d:'CSRF tokens, XSS protection via Blade, SQL injection prevention via Eloquent, bcrypt 12 rounds, rate limiting, session encryption.' },
      { ico:'🔌', t:'API Security',
        d:'Sanctum tokens, expiry & rotation, per-user and per-IP rate limits, CORS allow-list for known origins.' },
      { ico:'🗄️', t:'Data Protection',
        d:'Encrypted-at-rest sensitive fields, scheduled backups, full admin audit log, GDPR export/delete endpoints.' },
      { ico:'💵', t:'Financial Security',
        d:'All balance changes inside DB transactions, row locking on wallet ops, duplicate-tx prevention, withdrawal approval threshold.' },
      { ico:'🌐', t:'Infrastructure Security',
        d:'Cloudflare DDoS protection + WAF, SSL/TLS everywhere, non-root container, firewall rules on production host.' }
    ];

    el.innerHTML = shell(
      'Defense in Depth · Click a layer',
      `<div class="layer-stack" id="sl">
        ${layers.map((l,i) => `
          <div class="layer ${i===0?'on':''}" data-i="${i}">
            <span class="ico">${l.ico}</span>
            <div class="body">
              <p class="t">${l.t}</p>
              <p class="d">${l.d}</p>
            </div>
            <span class="chev">▸</span>
          </div>
        `).join('')}
      </div>`,
      'Fail-closed defaults · every layer independently monitored'
    );

    $$('.layer', el).forEach(l => l.addEventListener('click', () => {
      const wasOn = l.classList.contains('on');
      $$('.layer', el).forEach(x => x.classList.remove('on'));
      if (!wasOn) l.classList.add('on');
    }));
  };

  return { mount, registry };
})();
