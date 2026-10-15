/* ============================================================
   RACKSEPHNOX — Framework Data (Industrial Grade)
   Source: Racksephnox Framework Documentation (Technical Reference)
   Every framework carries a `demo` id consumed by modules.js
   ============================================================ */

window.RX_META = {
  name: "Racksephnox",
  tagline: "Global Cryptocurrency | Fintech | Gaming Empire",
  sig: "Infinite Spiral of Creation | 888 Hz | Golden Ratio Phi",
  paybill: "200999",
  account: "0333060036213",
  cname: "racksephnox.riqelsspiraltech.us.ci",
  version: "1.0.0-industrial",
  stats: [
    { num: "20",   lbl: "Frameworks" },
    { num: "60+",  lbl: "Database Tables" },
    { num: "40+",  lbl: "Controllers" },
    { num: "30+",  lbl: "Service Classes" },
    { num: "50+",  lbl: "Eloquent Models" },
    { num: "80+",  lbl: "Blade Views" },
    { num: "60+",  lbl: "API Endpoints" },
    { num: "50K+", lbl: "Lines of Code" }
  ]
};

window.RX_PRINCIPLES = [
  { ico: "🆔", t: "Unified Identity",     d: "One account for every module — crypto, banking, gaming, loans, referrals." },
  { ico: "🔍", t: "Transparent Ops",      d: "Full audit trail on every action, credit, and state change." },
  { ico: "📱", t: "Mobile-First",         d: "Designed for emerging markets, Paybill 200999 deposits, low-bandwidth friendly." },
  { ico: "🧩", t: "Modular Architecture", d: "Each framework independent — swap, scale, or disable without cascade failure." },
  { ico: "✨", t: "Golden Theme",         d: "Gold, black, ivory palette. Phi, lambda, and 888 Hz woven through the math." }
];

window.RX_STACK = [
  "PHP 8.4","Laravel 13","SQLite / PostgreSQL","Vite 5","TailwindCSS 3",
  "Alpine.js","KLineChart","Font Awesome 6","Breeze + Sanctum",
  "Cloudflare Tunnel","GitHub Actions","Docker Multi-Stage","Redis","SMTP"
];

window.RX_FRAMEWORKS = [

/* 1 ---------------------------------------------------------- */
{
  id: "platform-overview",
  num: 1,
  icon: "🌐",
  title: "Platform Overview",
  sub: "A unified digital wealth platform",
  brief: "Crypto, investment, gaming, loans, and referrals under one authenticated experience.",
  demo: "overview-stats",
  html: `
    <p>Racksephnox is a unified digital wealth platform combining cryptocurrency trading, investment products, gaming, loans, and referral systems into a single authenticated experience. Built on <strong>Laravel 13</strong> with a <strong>mobile-first</strong> philosophy and deployed globally via <strong>Cloudflare Tunnel</strong>.</p>

    <h2>Core Design Principles</h2>
    <ul>
      <li><strong>Unified identity</strong> — one account for every module</li>
      <li><strong>Transparent operations</strong> — full audit trail on every action</li>
      <li><strong>Mobile-first</strong> — designed for emerging markets</li>
      <li><strong>Modular architecture</strong> — each framework independent</li>
      <li><strong>Golden theme</strong> — gold, black, ivory palette</li>
    </ul>

    <h2>Platform Statistics</h2>
    <table>
      <tbody>
        <tr><td>Frameworks</td><td>15+ distinct systems</td></tr>
        <tr><td>Database Tables</td><td>60+ tables</td></tr>
        <tr><td>Controllers</td><td>40+ across web and API</td></tr>
        <tr><td>Services</td><td>30+ business logic classes</td></tr>
        <tr><td>Models</td><td>50+ Eloquent models</td></tr>
        <tr><td>Views</td><td>80+ blade templates</td></tr>
        <tr><td>API Endpoints</td><td>60+ REST endpoints</td></tr>
        <tr><td>Lines of Code</td><td>50,000+</td></tr>
      </tbody>
    </table>
  `
},

/* 2 ---------------------------------------------------------- */
{
  id: "technology-stack",
  num: 2,
  icon: "⚙️",
  title: "Technology Stack",
  sub: "The engine room",
  brief: "PHP 8.4, Laravel 13, Alpine, Tailwind, Docker, Cloudflare Tunnel.",
  demo: null,
  html: `
    <p>Every layer of the platform — from database driver to deployment pipeline — is pinned below.</p>

    <table>
      <tbody>
        <tr><td>Language</td><td>PHP 8.4</td></tr>
        <tr><td>Framework</td><td>Laravel 13</td></tr>
        <tr><td>Database</td><td>SQLite (dev) / PostgreSQL (production)</td></tr>
        <tr><td>Frontend Build</td><td>Vite 5</td></tr>
        <tr><td>CSS Framework</td><td>TailwindCSS 3</td></tr>
        <tr><td>JS Framework</td><td>Alpine.js + Vanilla</td></tr>
        <tr><td>Charts</td><td>KLineChart (candlesticks)</td></tr>
        <tr><td>Icons</td><td>Font Awesome 6 + Emoji</td></tr>
        <tr><td>Authentication</td><td>Laravel Breeze + Sanctum</td></tr>
        <tr><td>Deployment</td><td>Cloudflare Tunnel</td></tr>
        <tr><td>CI/CD</td><td>GitHub Actions</td></tr>
        <tr><td>Container</td><td>Docker multi-stage</td></tr>
        <tr><td>Cache</td><td>File (dev) / Redis (production)</td></tr>
        <tr><td>Queue</td><td>Database / Redis</td></tr>
        <tr><td>Mail</td><td>Log (dev) / SMTP (production)</td></tr>
      </tbody>
    </table>
  `
},

/* 3 ---------------------------------------------------------- */
{
  id: "authentication-framework",
  num: 3,
  icon: "🔐",
  title: "Authentication Framework",
  sub: "Registration, login, session, access control",
  brief: "Laravel Breeze with custom admin detection and rate limiting.",
  demo: "auth-simulator",
  html: `
    <p>Handles user registration, login, session management, and access control. Built on <strong>Laravel Breeze</strong> with custom enhancements for admin detection.</p>

    <h2>Database Tables</h2>
    <table>
      <thead><tr><th>Table</th><th>Purpose</th></tr></thead>
      <tbody>
        <tr><td>users</td><td>Core user records</td></tr>
        <tr><td>password_reset_tokens</td><td>Password reset flow</td></tr>
        <tr><td>personal_access_tokens</td><td>Sanctum API tokens</td></tr>
        <tr><td>sessions</td><td>Active web sessions</td></tr>
      </tbody>
    </table>

    <h2>Controllers</h2>
    <ul>
      <li><code>AuthenticatedSessionController</code> — login / logout</li>
      <li><code>RegisteredUserController</code> — new user creation</li>
      <li><code>PasswordResetLinkController</code> — forgot password</li>
      <li><code>NewPasswordController</code> — reset token validation</li>
      <li><code>EmailVerificationPromptController</code> — verify email</li>
    </ul>

    <h2>Workflow — Registration</h2>
    <ol>
      <li>User submits name, email, password</li>
      <li>Validation ensures unique email and strong password</li>
      <li>User record created with auto-verified email</li>
      <li><strong>Wallet + TradingAccount + TradingProfile</strong> created via <code>boot()</code></li>
      <li>Referral code auto-generated</li>
      <li>User redirected to dashboard</li>
    </ol>

    <h2>Workflow — Login</h2>
    <ol>
      <li>User submits email + password</li>
      <li>Rate limiter prevents brute force (5 attempts)</li>
      <li>On success, session regenerated</li>
      <li>Admin users redirected to <code>/admin</code></li>
      <li>Regular users redirected to <code>/dashboard</code></li>
    </ol>

    <h2>Security Features</h2>
    <ul>
      <li>Bcrypt hashing with 12 rounds</li>
      <li>CSRF tokens on every form</li>
      <li>Rate limiting on auth routes</li>
      <li>Email verification option</li>
      <li>Two-factor authentication schema ready</li>
    </ul>
  `
},

/* 4 ---------------------------------------------------------- */
{
  id: "dashboard-framework",
  num: 4,
  icon: "📊",
  title: "Dashboard Framework",
  sub: "Central hub after login",
  brief: "Aggregates wealth, banking, investments, loans, referrals and gaming into one overview.",
  demo: "dashboard-preview",
  html: `
    <p>Central hub after login. Aggregates data from every subsystem into a unified overview: wealth, banking, investments, loans, referrals, gaming.</p>

    <h2>Sections</h2>
    <ul>
      <li><strong>Wealth Pillars</strong> — balance, invested, profit, active holdings</li>
      <li><strong>Quick Navigation</strong> — 11 portals to every module</li>
      <li><strong>Lottery Jackpot</strong> — live progressive pool</li>
      <li><strong>Banking Center</strong> — deposit and withdrawal CTAs</li>
      <li><strong>Loans Widget</strong> — credit score, upcoming repayments</li>
      <li><strong>Referral Widget</strong> — count, bonus earned, share link</li>
      <li><strong>Trading Widget</strong> — balance, BTC price</li>
      <li><strong>Machine CTA</strong> — RX Series promo</li>
      <li><strong>Charts</strong> — 30-day profit, weekly activity, portfolio</li>
      <li><strong>Recent Transactions</strong> — last 10 entries</li>
    </ul>

    <h2>Data Aggregation</h2>
    <p><code>DashboardController::index()</code> queries <strong>12+ tables</strong> and caches heavy aggregates (BTC price, credit score, jackpot total) for <strong>30–300 seconds</strong>.</p>

    <h2>Real-Time Updates</h2>
    <ul>
      <li>Unread notifications: every <strong>30s</strong></li>
      <li>BTC price: every <strong>10s</strong></li>
      <li>Lottery jackpot: every <strong>15s</strong></li>
    </ul>
  `
},

/* 5 ---------------------------------------------------------- */
{
  id: "banking-center",
  num: 5,
  icon: "🏦",
  title: "Banking Center",
  sub: "Deposits & Withdrawals",
  brief: "All money in and out. Paybill 200999 in, mobile/bank out.",
  demo: "banking-flow",
  html: `
    <p>Handles all money movement in and out of the platform. Users deposit via <strong>M-Pesa Paybill 200999</strong> and withdraw to their mobile or bank account.</p>

    <h2>Database Tables</h2>
    <table>
      <thead><tr><th>Table</th><th>Purpose</th></tr></thead>
      <tbody>
        <tr><td>deposit_requests</td><td>User deposit submissions</td></tr>
        <tr><td>withdrawal_requests</td><td>User withdrawal requests</td></tr>
        <tr><td>mpesa_transactions</td><td>Raw M-Pesa transaction log</td></tr>
        <tr><td>user_bank_accounts</td><td>Saved bank accounts for withdrawals</td></tr>
        <tr><td>wallets</td><td>KES wallet per user</td></tr>
      </tbody>
    </table>

    <h2>Deposit Flow — Complete</h2>
    <ol>
      <li>User opens <code>/deposit</code></li>
      <li>Sees Paybill <strong>200999</strong> and Account <strong>0333060036213</strong></li>
      <li>Sends money via M-Pesa app</li>
      <li>Receives SMS with transaction code (e.g. <code>SFE4A8B2C1</code>)</li>
      <li>Enters code + amount on the deposit form</li>
      <li>System validates code format (10 chars, starts with letter)</li>
      <li>System checks code is not duplicated</li>
      <li>System attempts auto-match against uploaded statements</li>
      <li>Deposit stored with status = <code>pending_verification</code></li>
      <li>User sees "Awaiting Verification" page</li>
    </ol>

    <h2>Admin Verification Flow</h2>
    <ol>
      <li>Admin opens <code>/admin/deposits</code></li>
      <li>Sees pending list with code, amount, phone, user</li>
      <li><strong>Option A</strong>: Manually verify against M-Pesa app</li>
      <li><strong>Option B</strong>: Upload M-Pesa statement CSV — auto-matches</li>
      <li>Admin clicks Verify → wallet credited → transaction logged</li>
      <li>User notified via in-app notification</li>
    </ol>

    <h2>Withdrawal Flow</h2>
    <ol>
      <li>User requests withdrawal with amount + account</li>
      <li>System checks balance, caps, and KYC status</li>
      <li>Wallet debited immediately (<code>locked_balance</code>)</li>
      <li>Admin approves and sends via M-Pesa B2C</li>
      <li>Wallet unlocked → transaction completed</li>
    </ol>
  `
},

/* 6 ---------------------------------------------------------- */
{
  id: "mpesa-integration",
  num: 6,
  icon: "📲",
  title: "M-Pesa Integration & Verification",
  sub: "Manual + automated statement matching",
  brief: "Two-mode verification with confidence scoring and strict guardrails.",
  demo: "mpesa-validator",
  html: `
    <h2>Paybill Details</h2>
    <table>
      <tbody>
        <tr><td>Paybill Number</td><td>200999</td></tr>
        <tr><td>Account Number</td><td>0333060036213</td></tr>
        <tr><td>Transaction Code Format</td><td>10 chars, starts with letter</td></tr>
        <tr><td>Example Code</td><td>SFE4A8B2C1</td></tr>
      </tbody>
    </table>

    <h2>Verification Framework</h2>
    <p>Two modes work together: <strong>manual verification</strong> and <strong>automated statement matching</strong>.</p>

    <h3>Manual Verification</h3>
    <ul>
      <li>Admin opens M-Pesa app on phone</li>
      <li>Confirms transaction code exists in statement</li>
      <li>Clicks Verify in admin panel</li>
      <li>Wallet credited instantly</li>
    </ul>

    <h3>Automated Statement Matching</h3>
    <ul>
      <li>Admin exports M-Pesa statement as CSV</li>
      <li>Uploads via admin panel</li>
      <li>System parses every transaction row</li>
      <li>Auto-matches against pending deposits</li>
      <li>Confidence scores: <code>100</code> = exact, <code>90</code> = code only, <code>60</code> = fuzzy</li>
      <li>Admin reviews and approves matched rows</li>
    </ul>

    <h2>Services</h2>
    <ul>
      <li><code>DepositVerificationService</code> — core verification logic</li>
      <li><code>MpesaStatementParser</code> — CSV parsing</li>
    </ul>

    <h2>Security Guardrails</h2>
    <ul>
      <li>Code format validation (regex)</li>
      <li>Duplicate code detection</li>
      <li>Amount range validation (KES 10 to 1,000,000)</li>
      <li>Phone number format check</li>
      <li>Admin approval required for all credits</li>
    </ul>
  `
},

/* 7 ---------------------------------------------------------- */
{
  id: "crypto-trading",
  num: 7,
  icon: "📈",
  title: "Cryptocurrency Trading Framework",
  sub: "Real-time candlesticks, order book, buy/sell engine",
  brief: "Full trading console — BTCUSDT live, architecture ready for ETH, BNB.",
  demo: "crypto-ticker",
  html: `
    <p>Full-featured crypto trading console with real-time candlestick charts, order book, buy/sell engine, and portfolio management. Currently supports <strong>BTCUSDT</strong> with architecture ready for ETH, BNB, and others.</p>

    <h2>Database Tables</h2>
    <table>
      <thead><tr><th>Table</th><th>Purpose</th></tr></thead>
      <tbody>
        <tr><td>trading_pairs</td><td>Available pairs (BTCUSDT, ETHUSDT...)</td></tr>
        <tr><td>trade_orders</td><td>User buy/sell orders</td></tr>
        <tr><td>trading_candles</td><td>OHLCV candle data</td></tr>
        <tr><td>trading_accounts</td><td>Per-user trading balance</td></tr>
        <tr><td>btc_price_history</td><td>Historical BTC price data</td></tr>
      </tbody>
    </table>

    <h2>Chart Rendering</h2>
    <ul>
      <li>Library: <strong>KLineChart</strong> (MIT, ~40 KB gzipped)</li>
      <li>Delivered via Vite (bundled, no CDN)</li>
      <li>6 timeframes: 1m, 5m, 15m, 1h, 4h, 1d</li>
      <li>7 indicators: MA, EMA, BOLL, MACD, RSI, KDJ, OBV</li>
      <li><code>ResizeObserver</code> for responsive canvas</li>
      <li><code>requestAnimationFrame</code> throttling for 60 FPS</li>
      <li>Preloads adjacent intervals during idle time</li>
    </ul>

    <h2>Order Flow</h2>
    <ol>
      <li>User selects side (buy or sell)</li>
      <li>Enters amount and order type (market/limit/stop)</li>
      <li>Optional take-profit and stop-loss</li>
      <li>System validates against balance</li>
      <li>Market orders execute immediately at current price</li>
      <li>Limit orders placed in the book for matching</li>
    </ol>

    <h2>Services</h2>
    <ul>
      <li><code>TradingEngine</code> — market price, order book, order placement</li>
      <li><code>ChartService</code> — Binance sync + synthetic fallback</li>
      <li><code>TradingBonusService</code> — signup bonus + volume tracking</li>
    </ul>

    <h2>Real-Time Updates</h2>
    <ul>
      <li>Candles refresh every <strong>5 seconds</strong></li>
      <li>Order book refreshes every <strong>3 seconds</strong></li>
      <li>Auto-reconnect with exponential backoff</li>
      <li>Pauses when browser tab in background</li>
    </ul>
  `
},

/* 8 ---------------------------------------------------------- */
{
  id: "forex-trading",
  num: 8,
  icon: "💱",
  title: "Forex Trading Framework",
  sub: "Traditional pairs on the same engine",
  brief: "EUR/USD, GBP/USD, USD/JPY, USD/KES — shared order book & charts.",
  demo: "forex-converter",
  html: `
    <p>Extension of the crypto trading engine for traditional forex pairs. Shares the same order book, engine, and chart architecture, with forex-specific configurations.</p>

    <h2>Planned Pairs</h2>
    <ul>
      <li><strong>EUR/USD</strong> — most liquid globally</li>
      <li><strong>GBP/USD</strong> — major pair</li>
      <li><strong>USD/JPY</strong> — Asian session driver</li>
      <li><strong>USD/KES</strong> — Kenya-focused</li>
    </ul>

    <h2>Implementation Notes</h2>
    <ul>
      <li>Same <code>TradeOrder</code> table with <code>pair_id</code> differentiation</li>
      <li>Same <code>ChartService</code> — configure forex data source</li>
      <li>Leverage tiers configured per pair</li>
      <li>Spread applied to entry/exit prices</li>
    </ul>

    <h2>Regulatory Considerations</h2>
    <ul>
      <li>Forex is regulated in most jurisdictions</li>
      <li>KYC required above certain volumes</li>
      <li>Compliance officer role assigned</li>
    </ul>
  `
},

/* 9 ---------------------------------------------------------- */
{
  id: "investment-plans",
  num: 9,
  icon: "💎",
  title: "Investment Plans Framework",
  sub: "Lock KES, earn daily interest",
  brief: "Four default plans from Starter to Divine with automatic 24h accrual.",
  demo: "investment-roi",
  html: `
    <p>Traditional investment plans where users lock KES for a duration and earn daily interest. Interest is credited automatically every <strong>24 hours</strong>.</p>

    <h2>Database Tables</h2>
    <table>
      <thead><tr><th>Table</th><th>Purpose</th></tr></thead>
      <tbody>
        <tr><td>investment_plans</td><td>Plan definitions</td></tr>
        <tr><td>investments</td><td>User investments</td></tr>
      </tbody>
    </table>

    <h2>Default Plans</h2>
    <table>
      <thead><tr><th>Plan</th><th>Terms</th></tr></thead>
      <tbody>
        <tr><td>Starter</td><td>KES 1K–50K | 1.5%/day | 30 days</td></tr>
        <tr><td>Growth</td><td>KES 10K–250K | 2.25%/day | 30 days</td></tr>
        <tr><td>Premium</td><td>KES 50K–1M | 3%/day | 45 days</td></tr>
        <tr><td>Divine</td><td>KES 100K–5M | 6.28%/day | 14 days (88% ROI)</td></tr>
      </tbody>
    </table>

    <h2>Workflow</h2>
    <ol>
      <li>User selects plan and enters amount</li>
      <li>System validates against plan min/max</li>
      <li>Wallet debited immediately</li>
      <li>Investment record created with projected totals</li>
      <li>Daily profit accrues via scheduled job</li>
      <li>User can view progress and remaining days</li>
      <li>At maturity, user can withdraw total returns</li>
    </ol>

    <h2>Services</h2>
    <ul>
      <li><code>InterestCalculator</code> — daily/total profit math</li>
      <li><code>InvestmentService</code> — creation, accrual, withdrawal</li>
      <li><code>InvestmentManager</code> — plan stats and user holdings</li>
      <li><code>UnifiedInvestmentService</code> — combines with machines</li>
    </ul>
  `
},

/* 10 --------------------------------------------------------- */
{
  id: "rx-machine-series",
  num: 10,
  icon: "🛰️",
  title: "RX Machine Series",
  sub: "7 tiers × 3 VIP levels",
  brief: "RX0 Origin → RX6 Infinity. Golden-ratio VIP multipliers.",
  demo: "machine-calculator",
  html: `
    <p>Premium investment product with <strong>7 tiers</strong> (RX0 through RX6) and <strong>3 VIP levels</strong> per tier. Higher tiers offer higher returns and higher minimums.</p>

    <h2>Database Tables</h2>
    <table>
      <thead><tr><th>Table</th><th>Purpose</th></tr></thead>
      <tbody>
        <tr><td>machines</td><td>Machine tier definitions</td></tr>
        <tr><td>machine_vips</td><td>3 VIP levels per machine</td></tr>
        <tr><td>machine_investments</td><td>User holdings</td></tr>
      </tbody>
    </table>

    <h2>The 7 Machines</h2>
    <table>
      <thead><tr><th>Machine</th><th>Terms</th></tr></thead>
      <tbody>
        <tr><td>RX0 Origin</td><td>KES 1K–25K | 25% growth</td></tr>
        <tr><td>RX1 Aurora</td><td>KES 2K–50K | 30% growth</td></tr>
        <tr><td>RX2 Nova</td><td>KES 5K–100K | 38% growth</td></tr>
        <tr><td>RX3 Prism</td><td>KES 10K–250K | 45% growth</td></tr>
        <tr><td>RX4 Eclipse</td><td>KES 25K–500K | 55% growth</td></tr>
        <tr><td>RX5 Quantum</td><td>KES 50K–1M | 70% growth</td></tr>
        <tr><td>RX6 Infinity</td><td>KES 100K–2M | 88% growth</td></tr>
      </tbody>
    </table>

    <h2>VIP Tiers per Machine</h2>
    <ul>
      <li><strong>VIP 1:</strong> Baseline growth rate</li>
      <li><strong>VIP 2:</strong> Lambda-multiplied growth (×1.272)</li>
      <li><strong>VIP 3:</strong> Phi-multiplied growth (×1.618)</li>
    </ul>

    <h2>Golden Constants</h2>
    <ul>
      <li><code>phi = 1.61803398875</code> — divine proportion</li>
      <li><code>lambda = 1.27201964951</code> — √phi</li>
      <li><code>pi = 3.14159265359</code> — circular constant</li>
      <li><code>e = 2.71828182846</code> — natural growth</li>
    </ul>

    <h2>Cycle</h2>
    <ul>
      <li><strong>14-day</strong> investment cycle</li>
      <li>Daily profit accrual</li>
      <li>Early withdrawal penalty: <strong>20%</strong></li>
      <li>Referral bonus: <strong>5%</strong></li>
    </ul>
  `
},

/* 11 --------------------------------------------------------- */
{
  id: "btc-machines",
  num: 11,
  icon: "₿",
  title: "BTC Investment Machines",
  sub: "Bitcoin-backed returns",
  brief: "Tracks BTC price appreciation, auto-compound optional, 14-day cycle.",
  demo: "btc-appreciation",
  html: `
    <p>Bitcoin-specific investment machines that track BTC price appreciation and combine with trading profits. Shares the Machine architecture but uses BTC-based returns instead of fixed percentages.</p>

    <h2>Mechanism</h2>
    <ul>
      <li>User invests KES equivalent of BTC</li>
      <li>Machine tracks BTC price in real-time</li>
      <li>Profit = BTC price appreciation + machine bonus</li>
      <li>14-day cycle, same as RX Series</li>
      <li>Auto-compound option available</li>
    </ul>

    <h2>Integration</h2>
    <ul>
      <li>Uses <code>trading_candles</code> for BTC price data</li>
      <li>Syncs with <code>TradingEngine::getMarketPrice()</code></li>
      <li>Shares <code>machine_investments</code> table with tier flag</li>
    </ul>
  `
},

/* 12 --------------------------------------------------------- */
{
  id: "lottery-casino",
  num: 12,
  icon: "🎰",
  title: "Lottery & Casino Framework",
  sub: "Provably-fair gaming ecosystem",
  brief: "18 tables, 4-tier jackpots, achievements, missions, tournaments, guilds.",
  demo: "lottery-jackpot",
  html: `
    <p>Full gaming ecosystem with provably-fair spins, progressive jackpots, achievements, missions, streaks, tournaments, and guilds. Designed for engagement and retention.</p>

    <h2>Database Tables (18)</h2>
    <table>
      <thead><tr><th>Table</th><th>Purpose</th></tr></thead>
      <tbody>
        <tr><td>lottery_games</td><td>Game definitions</td></tr>
        <tr><td>lottery_symbols</td><td>Reel symbols</td></tr>
        <tr><td>lottery_payouts</td><td>Payout matrix</td></tr>
        <tr><td>lottery_spins</td><td>Every spin record</td></tr>
        <tr><td>lottery_jackpot_pools</td><td>4-tier progressive pool</td></tr>
        <tr><td>lottery_jackpot_wins</td><td>Jackpot win history</td></tr>
        <tr><td>lottery_achievements</td><td>Milestones</td></tr>
        <tr><td>lottery_user_achievements</td><td>Earned achievements</td></tr>
        <tr><td>lottery_daily_streaks</td><td>Streak tracking</td></tr>
        <tr><td>lottery_missions</td><td>Daily quests</td></tr>
        <tr><td>lottery_user_missions</td><td>Quest progress</td></tr>
        <tr><td>lottery_tournaments</td><td>Competitions</td></tr>
        <tr><td>lottery_tournament_entries</td><td>Scores</td></tr>
        <tr><td>lottery_guilds</td><td>Player collectives</td></tr>
        <tr><td>lottery_guild_members</td><td>Membership</td></tr>
        <tr><td>lottery_bonus_wheels</td><td>Wheel config</td></tr>
        <tr><td>lottery_bonus_wheel_spins</td><td>Wheel history</td></tr>
        <tr><td>lottery_fair_seeds</td><td>Provably-fair chain</td></tr>
      </tbody>
    </table>

    <h2>4-Tier Jackpot System</h2>
    <table>
      <thead><tr><th>Tier</th><th>Config</th></tr></thead>
      <tbody>
        <tr><td>Bronze</td><td>528 Hz | seed 10K | ceiling 500K</td></tr>
        <tr><td>Silver</td><td>639 Hz | seed 100K | ceiling 5M</td></tr>
        <tr><td>Gold</td><td>741 Hz | seed 1M | ceiling 50M</td></tr>
        <tr><td>Cosmic</td><td>963 Hz | seed 10M | ceiling 100M</td></tr>
      </tbody>
    </table>

    <h2>Provably Fair</h2>
    <ul>
      <li>Each spin produces <code>client_seed</code> + <code>server_seed</code> + <code>nonce</code></li>
      <li>HMAC-SHA256 produces verifiable hash</li>
      <li>User can rotate seed chain anytime</li>
      <li>Verification endpoint allows historical audit</li>
    </ul>

    <h2>Services</h2>
    <ul>
      <li><code>RngService</code> — provably-fair random generation</li>
      <li><code>LotteryService</code> — spin orchestrator</li>
      <li><code>JackpotPoolService</code> — 4-tier pool management</li>
      <li><code>AchievementService</code> — milestone detection</li>
      <li><code>StreakService</code> — daily streak rewards</li>
      <li><code>MissionService</code> — quest generation</li>
      <li><code>BonusWheelService</code> — weighted wheel</li>
      <li><code>TournamentService</code> — scoring and prizes</li>
      <li><code>GuildService</code> — collective management</li>
    </ul>

    <h2>Responsible Gaming</h2>
    <ul>
      <li>Daily/weekly/monthly loss caps</li>
      <li>Cool-down periods</li>
      <li>Self-exclusion (24h to 1 year)</li>
      <li>Session timeout at 60 min</li>
      <li>Reality checks every 30 min</li>
    </ul>
  `
},

/* 13 --------------------------------------------------------- */
{
  id: "loans-services",
  num: 13,
  icon: "🏛️",
  title: "Loans Services Framework",
  sub: "Credit scoring & amortization",
  brief: "Four products from Divine Micro to Infinity Mortgage. Score range 300–850.",
  demo: "loans-credit",
  html: `
    <p>Lending platform with credit scoring, tiered products, amortization scheduling, and default detection. Users build credit through on-time repayment.</p>

    <h2>Database Tables</h2>
    <table>
      <thead><tr><th>Table</th><th>Purpose</th></tr></thead>
      <tbody>
        <tr><td>loan_products</td><td>Product definitions</td></tr>
        <tr><td>loans</td><td>Active and historical loans</td></tr>
        <tr><td>loan_repayments</td><td>Amortization schedule</td></tr>
        <tr><td>loan_guarantors</td><td>Guarantor records</td></tr>
        <tr><td>loan_collaterals</td><td>Collateral registry</td></tr>
        <tr><td>loan_credit_scores</td><td>User credit scores</td></tr>
      </tbody>
    </table>

    <h2>Default Products</h2>
    <table>
      <thead><tr><th>Product</th><th>Terms</th></tr></thead>
      <tbody>
        <tr><td>Divine Micro</td><td>KES 500–20K | 8% flat | 7–30 days</td></tr>
        <tr><td>Golden Personal</td><td>KES 5K–200K | 15% reducing | 30–180 days</td></tr>
        <tr><td>Phi Business</td><td>KES 50K–2M | 12% reducing | 90–730 days</td></tr>
        <tr><td>Infinity Mortgage</td><td>KES 500K–20M | 9.5% | 1–10 years</td></tr>
      </tbody>
    </table>

    <h2>Credit Scoring</h2>
    <ul>
      <li>Base score: <strong>500</strong></li>
      <li><strong>+200</strong> for full repayment ratio</li>
      <li><strong>+5</strong> per on-time payment</li>
      <li><strong>−10</strong> per late payment</li>
      <li><strong>−150</strong> per default</li>
      <li>Range: <strong>300 to 850</strong></li>
    </ul>

    <h2>Workflow</h2>
    <ol>
      <li>User applies — amount, duration, frequency</li>
      <li>System checks credit score and exposure</li>
      <li>Admin approves or rejects</li>
      <li>Admin disburses — wallet credited</li>
      <li>Amortization schedule generated</li>
      <li>User repays — schedule updates</li>
      <li>Credit score recalculated after each payment</li>
    </ol>
  `
},

/* 14 --------------------------------------------------------- */
{
  id: "referral-system",
  num: 14,
  icon: "🔗",
  title: "Referral System",
  sub: "5% of referee's first investment",
  brief: "Unique code per user, share link, wallet credit on conversion.",
  demo: "referral-link",
  html: `
    <p>The growth engine — users earn bonuses for inviting others.</p>

    <h2>Mechanism</h2>
    <ul>
      <li>Every user gets a unique referral code</li>
      <li>Share link: <code>/register?ref=CODE</code></li>
      <li>Referrer earns <strong>5%</strong> of referee's first investment</li>
      <li>Bonus credited directly to wallet</li>
      <li>Referral count and total earned shown on dashboard</li>
    </ul>

    <h2>Database Fields</h2>
    <ul>
      <li><code>users.referral_code</code> — unique per user</li>
      <li><code>users.referred_by</code> — parent referrer ID</li>
      <li><code>transactions</code> where <code>type = 'referral_bonus'</code></li>
    </ul>
  `
},

/* 15 --------------------------------------------------------- */
{
  id: "notifications",
  num: 15,
  icon: "🔔",
  title: "Notifications Framework",
  sub: "In-app · Email · SMS",
  brief: "Event-driven notifications with per-channel opt-out preferences.",
  demo: "notifications-preview",
  html: `
    <p>Keeps users informed about transactions, wins, loans, and events via in-app, email, and SMS channels.</p>

    <h2>Channels</h2>
    <ul>
      <li><strong>In-app</strong> (database channel)</li>
      <li><strong>Email</strong> (SMTP)</li>
      <li><strong>SMS</strong> (future — Africa's Talking integration)</li>
    </ul>

    <h2>Events That Trigger Notifications</h2>
    <ul>
      <li>Deposit confirmed</li>
      <li>Withdrawal processed</li>
      <li>Big win (lottery)</li>
      <li>Jackpot won</li>
      <li>Investment matured</li>
      <li>Loan approved/rejected</li>
      <li>Referral bonus earned</li>
      <li>KYC approved/rejected</li>
    </ul>

    <h2>Preferences</h2>
    <ul>
      <li>User can opt out per channel</li>
      <li>Preferences stored in <code>users.notification_preferences</code> JSON</li>
      <li>Unread count cached and polled every 30s</li>
    </ul>
  `
},

/* 16 --------------------------------------------------------- */
{
  id: "kyc-verification",
  num: 16,
  icon: "🪪",
  title: "KYC & Verification",
  sub: "4 progressive verification tiers",
  brief: "Basic → Standard → Enhanced → VIP. Higher tiers unlock larger limits.",
  demo: "kyc-tiers",
  html: `
    <p>Know Your Customer — required for regulatory compliance and to prevent fraud. Users submit identity documents for verification.</p>

    <h2>KYC Levels</h2>
    <table>
      <thead><tr><th>Level</th><th>Requirements</th></tr></thead>
      <tbody>
        <tr><td>Basic</td><td>Email + phone verified</td></tr>
        <tr><td>Standard</td><td>National ID + selfie uploaded</td></tr>
        <tr><td>Enhanced</td><td>Proof of address + bank statement</td></tr>
        <tr><td>VIP</td><td>Full KYC + source of funds</td></tr>
      </tbody>
    </table>

    <h2>Database Tables</h2>
    <table>
      <thead><tr><th>Table / Field</th><th>Purpose</th></tr></thead>
      <tbody>
        <tr><td>kyc_documents</td><td>Uploaded documents</td></tr>
        <tr><td>users.kyc_status</td><td>Current status</td></tr>
        <tr><td>users.kyc_level</td><td>Verification tier</td></tr>
      </tbody>
    </table>

    <h2>Workflow</h2>
    <ol>
      <li>User uploads ID document</li>
      <li>Admin reviews in <code>/admin/kyc</code></li>
      <li>Approve → <code>kyc_status = verified</code></li>
      <li>Reject → user notified with reason</li>
      <li>Higher levels unlock larger limits</li>
    </ol>
  `
},

/* 17 --------------------------------------------------------- */
{
  id: "profile-settings",
  num: 17,
  icon: "👤",
  title: "User Profile & Settings",
  sub: "Account, security, preferences",
  brief: "Personal info, password, 2FA, notifications, bank accounts, sessions.",
  demo: null,
  html: `
    <p>Central page for user account management — personal info, password, notification preferences, bank accounts, and security settings.</p>

    <h2>Sections</h2>
    <ul>
      <li>Personal information (name, email, phone)</li>
      <li>Password update</li>
      <li>Two-factor authentication</li>
      <li>Notification preferences</li>
      <li>Bank account management</li>
      <li>Session management</li>
      <li>Account deletion</li>
    </ul>

    <h2>Security</h2>
    <ul>
      <li>Password confirmation required for sensitive changes</li>
      <li>Session invalidation on password change</li>
      <li>Email notification on every major change</li>
    </ul>
  `
},

/* 18 --------------------------------------------------------- */
{
  id: "admin-console",
  num: 18,
  icon: "🛡️",
  title: "Admin Console",
  sub: "Nine department back-ends",
  brief: "Deposits, withdrawals, KYC, investments, machines, trading, lottery, loans, reports.",
  demo: null,
  html: `
    <p>Back-office for managing every aspect of the platform. Nine department admins each have dedicated sections tailored to their responsibilities.</p>

    <h2>Admin Sections</h2>
    <ul>
      <li><strong>Dashboard</strong> — platform-wide stats and activity</li>
      <li><strong>Users</strong> — list, edit, suspend, verify</li>
      <li><strong>Deposits</strong> — verify, reject, upload statements</li>
      <li><strong>Withdrawals</strong> — approve, process, reject</li>
      <li><strong>KYC</strong> — review documents</li>
      <li><strong>Investments</strong> — manage plans</li>
      <li><strong>Machines</strong> — manage RX tiers</li>
      <li><strong>Trading</strong> — monitor order book</li>
      <li><strong>Lottery</strong> — games, symbols, jackpots, analytics</li>
      <li><strong>Loans</strong> — approve, reject, disburse</li>
      <li><strong>Reports</strong> — export CSV/PDF</li>
      <li><strong>Settings</strong> — platform configuration</li>
    </ul>

    <h2>Access Control</h2>
    <ul>
      <li>Admin middleware on every <code>/admin</code> route</li>
      <li><code>is_admin</code> flag in users table</li>
      <li>Separate layout (<code>admin.layouts.app</code>)</li>
    </ul>
  `
},

/* 19 --------------------------------------------------------- */
{
  id: "deployment-cicd",
  num: 19,
  icon: "🚀",
  title: "Deployment & CI/CD",
  sub: "GitHub Actions · Docker · Cloudflare Tunnel",
  brief: "Multi-arch images to GHCR, Alpine runtime, named tunnel with WAF.",
  demo: "deploy-pipeline",
  html: `
    <h2>CI/CD Pipeline</h2>
    <ul>
      <li>GitHub Actions runs on every push to <code>spiral</code> branch</li>
      <li>Workflow: <strong>Laravel CI</strong> — runs tests</li>
      <li>Workflow: <strong>Docker Build</strong> — builds multi-arch image</li>
      <li>Image pushed to GitHub Container Registry (<strong>GHCR</strong>)</li>
      <li>Both run in parallel, cancel outdated runs</li>
    </ul>

    <h2>Docker</h2>
    <ul>
      <li>Multi-stage build — vendor, frontend, runtime</li>
      <li>Alpine base — small image size</li>
      <li>PHP 8.4 FPM with all extensions</li>
      <li>OPcache enabled for production</li>
      <li>Non-root user (<code>www-data</code>)</li>
      <li>Health check endpoint</li>
    </ul>

    <h2>Cloudflare Tunnel</h2>
    <ul>
      <li>Public HTTPS URL without public IP</li>
      <li>Named tunnel for <code>racksephnox.us.cj</code></li>
      <li>Auto-reconnect on network changes</li>
      <li>DDoS protection and WAF included</li>
      <li>Free SSL on edge</li>
    </ul>
  `
},

/* 20 --------------------------------------------------------- */
{
  id: "security-architecture",
  num: 20,
  icon: "🔒",
  title: "Security Architecture",
  sub: "Defense in depth",
  brief: "App, API, data, financial, infrastructure — every layer hardened.",
  demo: "security-layers",
  html: `
    <h2>Application Security</h2>
    <ul>
      <li>CSRF protection on all forms</li>
      <li>SQL injection prevention via Eloquent</li>
      <li>XSS prevention via Blade escaping</li>
      <li>Password hashing with bcrypt 12 rounds</li>
      <li>Rate limiting on auth and sensitive routes</li>
      <li>Two-factor authentication schema ready</li>
      <li>Session encryption and regeneration</li>
    </ul>

    <h2>API Security</h2>
    <ul>
      <li>Sanctum tokens for API authentication</li>
      <li>Token expiry and rotation</li>
      <li>Rate limiting per user and per IP</li>
      <li>CORS configured for known origins</li>
    </ul>

    <h2>Data Protection</h2>
    <ul>
      <li>Sensitive fields encrypted at rest</li>
      <li>Database backups scheduled</li>
      <li>Audit log for every admin action</li>
      <li>GDPR-compliant data export/deletion</li>
    </ul>

    <h2>Financial Security</h2>
    <ul>
      <li>All balance changes within transactions</li>
      <li>Row locking on wallet operations</li>
      <li>Duplicate transaction prevention</li>
      <li>Referral abuse detection</li>
      <li>Withdrawal approval required above threshold</li>
    </ul>

    <h2>Infrastructure Security</h2>
    <ul>
      <li>Cloudflare DDoS protection</li>
      <li>SSL/TLS on all traffic</li>
      <li>Non-root container user</li>
      <li>Firewall rules on production server</li>
    </ul>
  `
}

];
