#!/usr/bin/env python3
"""Racksephnox · Framework Documentation"""
from fpdf import FPDF
from datetime import datetime

REPLACEMENTS = {
    '\u2014': '-', '\u2013': '-', '\u2018': "'", '\u2019': "'",
    '\u201C': '"', '\u201D': '"', '\u2026': '...', '\u00B7': '-',
    '\u2022': '*', '\u00D7': 'x', '\u00B0': 'deg',
    '\u2264': '<=', '\u2265': '>=', '\u03C6': 'phi',
    '\u03BB': 'lam', '\u03C0': 'pi',
    '\u2192': '->', '\u2190': '<-', '\u2705': '[OK]',
    '\u274C': '[X]', '\u2605': '*', '\u2606': '*',
    '\u2028': ' ', '\u2029': ' ', '\u00A0': ' ',
    '\u2011': '-', '\u2012': '-', '\u2015': '-',
}

def sanitize(text):
    if not isinstance(text, str):
        text = str(text)
    for k, v in REPLACEMENTS.items():
        text = text.replace(k, v)
    return text.encode('latin-1', 'replace').decode('latin-1')


class DocsPDF(FPDF):
    def header(self):
        if self.page_no() > 1:
            self.set_font('Helvetica', 'I', 8)
            self.set_text_color(120, 120, 120)
            self.cell(0, 8, 'Racksephnox | Framework Documentation', 0, 0, 'L')
            self.cell(0, 8, 'Internal', 0, 1, 'R')
            self.set_draw_color(212, 175, 55)
            self.set_line_width(0.3)
            self.line(10, 18, 200, 18)
            self.ln(6)

    def footer(self):
        self.set_y(-15)
        self.set_font('Helvetica', 'I', 8)
        self.set_text_color(120, 120, 120)
        self.cell(0, 8, f'Page {self.page_no()}', 0, 0, 'C')

    def h1(self, t):
        self.set_font('Helvetica', 'B', 16)
        self.set_text_color(184, 134, 11)
        self.ln(4)
        self.multi_cell(0, 8, sanitize(t))
        self.set_draw_color(184, 134, 11)
        self.set_line_width(0.4)
        self.line(15, self.get_y() + 1, 195, self.get_y() + 1)
        self.ln(3)

    def h2(self, t):
        self.set_font('Helvetica', 'B', 12)
        self.set_text_color(80, 60, 20)
        self.ln(3)
        self.multi_cell(0, 6, sanitize(t))
        self.ln(1)

    def h3(self, t):
        self.set_font('Helvetica', 'B', 10.5)
        self.set_text_color(120, 90, 30)
        self.ln(1)
        self.multi_cell(0, 6, sanitize(t))

    def body(self, t, size=10.5):
        self.set_font('Helvetica', '', size)
        self.set_text_color(30, 30, 30)
        self.multi_cell(0, 6, sanitize(t))
        self.ln(2)

    def bullet(self, t, indent=15, size=10.5):
        self.set_font('Helvetica', '', size)
        self.set_text_color(30, 30, 30)
        self.set_x(indent)
        self.multi_cell(0, 6, sanitize('- ' + t))
        self.ln(0.4)

    def code(self, t, size=8.5):
        self.set_font('Courier', '', size)
        self.set_fill_color(245, 245, 245)
        self.set_text_color(20, 20, 80)
        self.multi_cell(0, 4.8, sanitize(t), 0, 'L', True)
        self.ln(2)

    def table(self, rows, col1=60, col2=125, size=10):
        for i, (k, v) in enumerate(rows):
            fill = (i % 2 == 0)
            self.set_fill_color(245, 240, 220) if fill else self.set_fill_color(255, 255, 255)
            self.set_text_color(60, 40, 10)
            self.set_font('Helvetica', 'B', size)
            self.cell(col1, 7.5, sanitize('  ' + k), 1, 0, 'L', fill)
            self.set_font('Helvetica', '', size)
            self.set_text_color(30, 30, 30)
            self.cell(col2, 7.5, sanitize('  ' + v), 1, 1, 'L', fill)
        self.ln(3)


pdf = DocsPDF(orientation='P', unit='mm', format='A4')
pdf.set_auto_page_break(auto=True, margin=20)
pdf.set_margins(15, 20, 15)

# ── COVER ──
pdf.add_page()
pdf.set_fill_color(10, 10, 20)
pdf.rect(0, 0, 210, 297, 'F')
pdf.set_fill_color(184, 134, 11)
pdf.rect(0, 40, 210, 2, 'F')
pdf.rect(0, 250, 210, 2, 'F')
pdf.set_y(75)
pdf.set_font('Helvetica', 'B', 30)
pdf.set_text_color(255, 215, 0)
pdf.cell(0, 15, 'RACKSEPHNOX', 0, 1, 'C')
pdf.set_font('Helvetica', '', 13)
pdf.set_text_color(212, 175, 55)
pdf.cell(0, 8, sanitize('Global Cryptocurrency | Fintech | Gaming Empire'), 0, 1, 'C')
pdf.ln(20)
pdf.set_font('Helvetica', 'B', 22)
pdf.set_text_color(245, 245, 230)
pdf.cell(0, 12, 'Framework Documentation', 0, 1, 'C')
pdf.cell(0, 12, 'Technical Reference', 0, 1, 'C')
pdf.ln(12)
pdf.set_font('Helvetica', 'I', 11)
pdf.set_text_color(180, 180, 180)
pdf.cell(0, 8, sanitize('Every System | Every Module | Every Workflow'), 0, 1, 'C')
pdf.set_y(240)
pdf.set_font('Helvetica', '', 10)
pdf.set_text_color(200, 200, 200)
pdf.cell(0, 6, sanitize(f'Generated: {datetime.now().strftime("%d %B %Y")}'), 0, 1, 'C')
pdf.set_y(260)
pdf.set_font('Helvetica', 'B', 9)
pdf.set_text_color(184, 134, 11)
pdf.cell(0, 5, 'CONFIDENTIAL | FOR TEAM AND INVESTORS', 0, 1, 'C')

# ── TOC ──
pdf.add_page()
pdf.h1('Table of Contents')
pdf.body(
    "1.  Platform Overview\n"
    "2.  Technology Stack\n"
    "3.  Authentication Framework\n"
    "4.  Dashboard Framework\n"
    "5.  Banking Center (Deposits & Withdrawals)\n"
    "6.  M-Pesa Integration & Verification\n"
    "7.  Cryptocurrency Trading Framework\n"
    "8.  Forex Trading Framework\n"
    "9.  Investment Plans Framework\n"
    "10. RX Machine Series\n"
    "11. BTC Investment Machines\n"
    "12. Lottery & Casino Framework\n"
    "13. Loans Services Framework\n"
    "14. Referral System\n"
    "15. Notifications Framework\n"
    "16. KYC & Verification\n"
    "17. User Profile & Settings\n"
    "18. Admin Console\n"
    "19. Deployment & CI/CD\n"
    "20. Security Architecture"
)

# ── 1 ──
pdf.add_page()
pdf.h1('1. Platform Overview')
pdf.body(
    "Racksephnox is a unified digital wealth platform combining cryptocurrency "
    "trading, investment products, gaming, loans, and referral systems into a "
    "single authenticated experience. Built on Laravel 13 with a mobile-first "
    "philosophy and deployed globally via Cloudflare Tunnel."
)
pdf.h2('Core Design Principles')
pdf.bullet("Unified identity - one account for every module")
pdf.bullet("Transparent operations - full audit trail on every action")
pdf.bullet("Mobile-first - designed for emerging markets")
pdf.bullet("Modular architecture - each framework independent")
pdf.bullet("Golden theme - gold, black, ivory palette")
pdf.h2('Platform Statistics')
pdf.table([
    ('Frameworks', '15+ distinct systems'),
    ('Database Tables', '60+ tables'),
    ('Controllers', '40+ across web and API'),
    ('Services', '30+ business logic classes'),
    ('Models', '50+ Eloquent models'),
    ('Views', '80+ blade templates'),
    ('API Endpoints', '60+ REST endpoints'),
    ('Lines of Code', '50,000+'),
])

# ── 2 ──
pdf.add_page()
pdf.h1('2. Technology Stack')
pdf.table([
    ('Language', 'PHP 8.4'),
    ('Framework', 'Laravel 13'),
    ('Database', 'SQLite (dev) / PostgreSQL (production)'),
    ('Frontend Build', 'Vite 5'),
    ('CSS Framework', 'TailwindCSS 3'),
    ('JS Framework', 'Alpine.js + Vanilla'),
    ('Charts', 'KLineChart (candlesticks)'),
    ('Icons', 'Font Awesome 6 + Emoji'),
    ('Authentication', 'Laravel Breeze + Sanctum'),
    ('Deployment', 'Cloudflare Tunnel'),
    ('CI/CD', 'GitHub Actions'),
    ('Container', 'Docker multi-stage'),
    ('Cache', 'File (dev) / Redis (production)'),
    ('Queue', 'Database / Redis'),
    ('Mail', 'Log (dev) / SMTP (production)'),
])

# ── 3 ──
pdf.add_page()
pdf.h1('3. Authentication Framework')
pdf.h2('Purpose')
pdf.body(
    "Handles user registration, login, session management, and access control. "
    "Built on Laravel Breeze with custom enhancements for admin detection."
)
pdf.h2('Database Tables')
pdf.table([
    ('users', 'Core user records'),
    ('password_reset_tokens', 'Password reset flow'),
    ('personal_access_tokens', 'Sanctum API tokens'),
    ('sessions', 'Active web sessions'),
])
pdf.h2('Controllers')
pdf.bullet("AuthenticatedSessionController - login/logout")
pdf.bullet("RegisteredUserController - new user creation")
pdf.bullet("PasswordResetLinkController - forgot password")
pdf.bullet("NewPasswordController - reset token validation")
pdf.bullet("EmailVerificationPromptController - verify email")
pdf.h2('Workflow - Registration')
pdf.bullet("User submits name, email, password")
pdf.bullet("Validation ensures unique email and strong password")
pdf.bullet("User record created with auto-verified email")
pdf.bullet("Wallet + TradingAccount + TradingProfile created via boot()")
pdf.bullet("Referral code auto-generated")
pdf.bullet("User redirected to dashboard")
pdf.h2('Workflow - Login')
pdf.bullet("User submits email + password")
pdf.bullet("Rate limiter prevents brute force (5 attempts)")
pdf.bullet("On success, session regenerated")
pdf.bullet("Admin users redirected to /admin")
pdf.bullet("Regular users redirected to /dashboard")
pdf.h2('Security Features')
pdf.bullet("Bcrypt hashing with 12 rounds")
pdf.bullet("CSRF tokens on every form")
pdf.bullet("Rate limiting on auth routes")
pdf.bullet("Email verification option")
pdf.bullet("Two-factor authentication schema ready")

# ── 4 ──
pdf.add_page()
pdf.h1('4. Dashboard Framework')
pdf.h2('Purpose')
pdf.body(
    "Central hub after login. Aggregates data from every subsystem into a "
    "unified overview: wealth, banking, investments, loans, referrals, gaming."
)
pdf.h2('Sections')
pdf.bullet("Wealth Pillars - balance, invested, profit, active holdings")
pdf.bullet("Quick Navigation - 11 portals to every module")
pdf.bullet("Lottery Jackpot - live progressive pool")
pdf.bullet("Banking Center - deposit and withdrawal CTAs")
pdf.bullet("Loans Widget - credit score, upcoming repayments")
pdf.bullet("Referral Widget - count, bonus earned, share link")
pdf.bullet("Trading Widget - balance, BTC price")
pdf.bullet("Machine CTA - RX Series promo")
pdf.bullet("Charts - 30-day profit, weekly activity, portfolio")
pdf.bullet("Recent Transactions - last 10 entries")
pdf.h2('Data Aggregation')
pdf.body(
    "DashboardController::index() queries 12+ tables and caches heavy "
    "aggregates (BTC price, credit score, jackpot total) for 30-300 seconds."
)
pdf.h2('Real-Time Updates')
pdf.bullet("Unread notifications: every 30s")
pdf.bullet("BTC price: every 10s")
pdf.bullet("Lottery jackpot: every 15s")

# ── 5 ──
pdf.add_page()
pdf.h1('5. Banking Center (Deposits & Withdrawals)')
pdf.h2('Purpose')
pdf.body(
    "Handles all money movement in and out of the platform. Users deposit "
    "via M-Pesa Paybill 200999 and withdraw to their mobile or bank account."
)
pdf.h2('Database Tables')
pdf.table([
    ('deposit_requests', 'User deposit submissions'),
    ('withdrawal_requests', 'User withdrawal requests'),
    ('mpesa_transactions', 'Raw M-Pesa transaction log'),
    ('user_bank_accounts', 'Saved bank accounts for withdrawals'),
    ('wallets', 'KES wallet per user'),
])
pdf.h2('Deposit Flow - Complete')
pdf.bullet("User opens /deposit")
pdf.bullet("Sees Paybill 200999 and Account 0333060036213")
pdf.bullet("Sends money via M-Pesa app")
pdf.bullet("Receives SMS with transaction code (e.g. SFE4A8B2C1)")
pdf.bullet("Enters code + amount on the deposit form")
pdf.bullet("System validates code format (10 chars, starts with letter)")
pdf.bullet("System checks code is not duplicated")
pdf.bullet("System attempts auto-match against uploaded statements")
pdf.bullet("Deposit stored with status = pending_verification")
pdf.bullet("User sees 'Awaiting Verification' page")
pdf.h2('Admin Verification Flow')
pdf.bullet("Admin opens /admin/deposits")
pdf.bullet("Sees pending list with code, amount, phone, user")
pdf.bullet("Option A: Manually verify against M-Pesa app")
pdf.bullet("Option B: Upload M-Pesa statement CSV - auto-matches")
pdf.bullet("Admin clicks Verify - wallet credited - transaction logged")
pdf.bullet("User notified via in-app notification")
pdf.h2('Withdrawal Flow')
pdf.bullet("User requests withdrawal with amount + account")
pdf.bullet("System checks balance, caps, and KYC status")
pdf.bullet("Wallet debited immediately (locked_balance)")
pdf.bullet("Admin approves and sends via M-Pesa B2C")
pdf.bullet("Wallet unlocked - transaction completed")

# ── 6 ──
pdf.add_page()
pdf.h1('6. M-Pesa Integration & Verification')
pdf.h2('Paybill Details')
pdf.table([
    ('Paybill Number', '200999'),
    ('Account Number', '0333060036213'),
    ('Transaction Code Format', '10 chars, starts with letter'),
    ('Example Code', 'SFE4A8B2C1'),
])
pdf.h2('Verification Framework')
pdf.body("Two modes work together: manual verification and automated statement matching.")
pdf.h3('Manual Verification')
pdf.bullet("Admin opens M-Pesa app on phone")
pdf.bullet("Confirms transaction code exists in statement")
pdf.bullet("Clicks Verify in admin panel")
pdf.bullet("Wallet credited instantly")
pdf.h3('Automated Statement Matching')
pdf.bullet("Admin exports M-Pesa statement as CSV")
pdf.bullet("Uploads via admin panel")
pdf.bullet("System parses every transaction row")
pdf.bullet("Auto-matches against pending deposits")
pdf.bullet("Confidence scores: 100 = exact, 90 = code only, 60 = fuzzy")
pdf.bullet("Admin reviews and approves matched rows")
pdf.h2('Services')
pdf.bullet("DepositVerificationService - core verification logic")
pdf.bullet("MpesaStatementParser - CSV parsing")
pdf.h2('Security Guardrails')
pdf.bullet("Code format validation (regex)")
pdf.bullet("Duplicate code detection")
pdf.bullet("Amount range validation (KES 10 to 1,000,000)")
pdf.bullet("Phone number format check")
pdf.bullet("Admin approval required for all credits")

# ── 7 ──
pdf.add_page()
pdf.h1('7. Cryptocurrency Trading Framework')
pdf.h2('Purpose')
pdf.body(
    "Full-featured crypto trading console with real-time candlestick charts, "
    "order book, buy/sell engine, and portfolio management. Currently supports "
    "BTCUSDT with architecture ready for ETH, BNB, and others."
)
pdf.h2('Database Tables')
pdf.table([
    ('trading_pairs', 'Available pairs (BTCUSDT, ETHUSDT...)'),
    ('trade_orders', 'User buy/sell orders'),
    ('trading_candles', 'OHLCV candle data'),
    ('trading_accounts', 'Per-user trading balance'),
    ('btc_price_history', 'Historical BTC price data'),
])
pdf.h2('Chart Rendering')
pdf.bullet("Library: KLineChart (MIT, ~40 KB gzipped)")
pdf.bullet("Delivered via Vite (bundled, no CDN)")
pdf.bullet("6 timeframes: 1m, 5m, 15m, 1h, 4h, 1d")
pdf.bullet("7 indicators: MA, EMA, BOLL, MACD, RSI, KDJ, OBV")
pdf.bullet("ResizeObserver for responsive canvas")
pdf.bullet("requestAnimationFrame throttling for 60 FPS")
pdf.bullet("Preloads adjacent intervals during idle time")
pdf.h2('Order Flow')
pdf.bullet("User selects side (buy or sell)")
pdf.bullet("Enters amount and order type (market/limit/stop)")
pdf.bullet("Optional take-profit and stop-loss")
pdf.bullet("System validates against balance")
pdf.bullet("Market orders execute immediately at current price")
pdf.bullet("Limit orders placed in the book for matching")
pdf.h2('Services')
pdf.bullet("TradingEngine - market price, order book, order placement")
pdf.bullet("ChartService - Binance sync + synthetic fallback")
pdf.bullet("TradingBonusService - signup bonus + volume tracking")
pdf.h2('Real-Time Updates')
pdf.bullet("Candles refresh every 5 seconds")
pdf.bullet("Order book refreshes every 3 seconds")
pdf.bullet("Auto-reconnect with exponential backoff")
pdf.bullet("Pauses when browser tab in background")

# ── 8 ──
pdf.add_page()
pdf.h1('8. Forex Trading Framework')
pdf.h2('Purpose')
pdf.body(
    "Extension of the crypto trading engine for traditional forex pairs. "
    "Shares the same order book, engine, and chart architecture, with "
    "forex-specific configurations."
)
pdf.h2('Planned Pairs')
pdf.bullet("EUR/USD - most liquid globally")
pdf.bullet("GBP/USD - major pair")
pdf.bullet("USD/JPY - Asian session driver")
pdf.bullet("USD/KES - Kenya-focused")
pdf.h2('Implementation Notes')
pdf.bullet("Same TradeOrder table with pair_id differentiation")
pdf.bullet("Same ChartService - configure forex data source")
pdf.bullet("Leverage tiers configured per pair")
pdf.bullet("Spread applied to entry/exit prices")
pdf.h2('Regulatory Considerations')
pdf.bullet("Forex is regulated in most jurisdictions")
pdf.bullet("KYC required above certain volumes")
pdf.bullet("Compliance officer role assigned")

# ── 9 ──
pdf.add_page()
pdf.h1('9. Investment Plans Framework')
pdf.h2('Purpose')
pdf.body(
    "Traditional investment plans where users lock KES for a duration and "
    "earn daily interest. Interest is credited automatically every 24 hours."
)
pdf.h2('Database Tables')
pdf.table([
    ('investment_plans', 'Plan definitions'),
    ('investments', 'User investments'),
])
pdf.h2('Default Plans')
pdf.table([
    ('Starter', 'KES 1K-50K | 1.5%/day | 30 days'),
    ('Growth', 'KES 10K-250K | 2.25%/day | 30 days'),
    ('Premium', 'KES 50K-1M | 3%/day | 45 days'),
    ('Divine', 'KES 100K-5M | 6.28%/day | 14 days (88% ROI)'),
])
pdf.h2('Workflow')
pdf.bullet("User selects plan and enters amount")
pdf.bullet("System validates against plan min/max")
pdf.bullet("Wallet debited immediately")
pdf.bullet("Investment record created with projected totals")
pdf.bullet("Daily profit accrues via scheduled job")
pdf.bullet("User can view progress and remaining days")
pdf.bullet("At maturity, user can withdraw total returns")
pdf.h2('Services')
pdf.bullet("InterestCalculator - daily/total profit math")
pdf.bullet("InvestmentService - creation, accrual, withdrawal")
pdf.bullet("InvestmentManager - plan stats and user holdings")
pdf.bullet("UnifiedInvestmentService - combines with machines")

# ── 10 ──
pdf.add_page()
pdf.h1('10. RX Machine Series')
pdf.h2('Purpose')
pdf.body(
    "Premium investment product with 7 tiers (RX0 through RX6) and 3 VIP "
    "levels per tier. Higher tiers offer higher returns and higher minimums."
)
pdf.h2('Database Tables')
pdf.table([
    ('machines', 'Machine tier definitions'),
    ('machine_vips', '3 VIP levels per machine'),
    ('machine_investments', 'User holdings'),
])
pdf.h2('The 7 Machines')
pdf.table([
    ('RX0 Origin', 'KES 1K-25K | 25% growth'),
    ('RX1 Aurora', 'KES 2K-50K | 30% growth'),
    ('RX2 Nova', 'KES 5K-100K | 38% growth'),
    ('RX3 Prism', 'KES 10K-250K | 45% growth'),
    ('RX4 Eclipse', 'KES 25K-500K | 55% growth'),
    ('RX5 Quantum', 'KES 50K-1M | 70% growth'),
    ('RX6 Infinity', 'KES 100K-2M | 88% growth'),
])
pdf.h2('VIP Tiers per Machine')
pdf.bullet("VIP 1: Baseline growth rate")
pdf.bullet("VIP 2: Lambda-multiplied growth (x1.272)")
pdf.bullet("VIP 3: Phi-multiplied growth (x1.618)")
pdf.h2('Golden Constants')
pdf.bullet("phi = 1.61803398875 - divine proportion")
pdf.bullet("lambda = 1.27201964951 - sqrt(phi)")
pdf.bullet("pi = 3.14159265359 - circular constant")
pdf.bullet("e = 2.71828182846 - natural growth")
pdf.h2('Cycle')
pdf.bullet("14-day investment cycle")
pdf.bullet("Daily profit accrual")
pdf.bullet("Early withdrawal penalty: 20%")
pdf.bullet("Referral bonus: 5%")

# ── 11 ──
pdf.add_page()
pdf.h1('11. BTC Investment Machines')
pdf.h2('Purpose')
pdf.body(
    "Bitcoin-specific investment machines that track BTC price appreciation "
    "and combine with trading profits. Shares the Machine architecture but "
    "uses BTC-based returns instead of fixed percentages."
)
pdf.h2('Mechanism')
pdf.bullet("User invests KES equivalent of BTC")
pdf.bullet("Machine tracks BTC price in real-time")
pdf.bullet("Profit = BTC price appreciation + machine bonus")
pdf.bullet("14-day cycle, same as RX Series")
pdf.bullet("Auto-compound option available")
pdf.h2('Integration')
pdf.bullet("Uses trading_candles for BTC price data")
pdf.bullet("Syncs with TradingEngine::getMarketPrice()")
pdf.bullet("Shares machine_investments table with tier flag")

# ── 12 ──
pdf.add_page()
pdf.h1('12. Lottery & Casino Framework')
pdf.h2('Purpose')
pdf.body(
    "Full gaming ecosystem with provably-fair spins, progressive jackpots, "
    "achievements, missions, streaks, tournaments, and guilds. Designed for "
    "engagement and retention."
)
pdf.h2('Database Tables (18)')
pdf.table([
    ('lottery_games', 'Game definitions'),
    ('lottery_symbols', 'Reel symbols'),
    ('lottery_payouts', 'Payout matrix'),
    ('lottery_spins', 'Every spin record'),
    ('lottery_jackpot_pools', '4-tier progressive pools'),
    ('lottery_jackpot_wins', 'Jackpot win history'),
    ('lottery_achievements', 'Milestones'),
    ('lottery_user_achievements', 'Earned achievements'),
    ('lottery_daily_streaks', 'Streak tracking'),
    ('lottery_missions', 'Daily quests'),
    ('lottery_user_missions', 'Quest progress'),
    ('lottery_tournaments', 'Competitions'),
    ('lottery_tournament_entries', 'Scores'),
    ('lottery_guilds', 'Player collectives'),
    ('lottery_guild_members', 'Membership'),
    ('lottery_bonus_wheels', 'Wheel config'),
    ('lottery_bonus_wheel_spins', 'Wheel history'),
    ('lottery_fair_seeds', 'Provably-fair chain'),
])
pdf.h2('4-Tier Jackpot System')
pdf.table([
    ('Bronze', '528 Hz | seed 10K | ceiling 500K'),
    ('Silver', '639 Hz | seed 100K | ceiling 5M'),
    ('Gold', '741 Hz | seed 1M | ceiling 50M'),
    ('Cosmic', '963 Hz | seed 10M | ceiling 100M'),
])
pdf.h2('Provably Fair')
pdf.bullet("Each spin produces client_seed + server_seed + nonce")
pdf.bullet("HMAC-SHA256 produces verifiable hash")
pdf.bullet("User can rotate seed chain anytime")
pdf.bullet("Verification endpoint allows historical audit")
pdf.h2('Services')
pdf.bullet("RngService - provably-fair random generation")
pdf.bullet("LotteryService - spin orchestrator")
pdf.bullet("JackpotPoolService - 4-tier pool management")
pdf.bullet("AchievementService - milestone detection")
pdf.bullet("StreakService - daily streak rewards")
pdf.bullet("MissionService - quest generation")
pdf.bullet("BonusWheelService - weighted wheel")
pdf.bullet("TournamentService - scoring and prizes")
pdf.bullet("GuildService - collective management")
pdf.h2('Responsible Gaming')
pdf.bullet("Daily/weekly/monthly loss caps")
pdf.bullet("Cool-down periods")
pdf.bullet("Self-exclusion (24h to 1 year)")
pdf.bullet("Session timeout at 60 min")
pdf.bullet("Reality checks every 30 min")

# ── 13 ──
pdf.add_page()
pdf.h1('13. Loans Services Framework')
pdf.h2('Purpose')
pdf.body(
    "Lending platform with credit scoring, tiered products, amortization "
    "scheduling, and default detection. Users build credit through on-time "
    "repayment."
)
pdf.h2('Database Tables')
pdf.table([
    ('loan_products', 'Product definitions'),
    ('loans', 'Active and historical loans'),
    ('loan_repayments', 'Amortization schedule'),
    ('loan_guarantors', 'Guarantor records'),
    ('loan_collaterals', 'Collateral registry'),
    ('loan_credit_scores', 'User credit scores'),
])
pdf.h2('Default Products')
pdf.table([
    ('Divine Micro', 'KES 500-20K | 8% flat | 7-30 days'),
    ('Golden Personal', 'KES 5K-200K | 15% reducing | 30-180 days'),
    ('Phi Business', 'KES 50K-2M | 12% reducing | 90-730 days'),
    ('Infinity Mortgage', 'KES 500K-20M | 9.5% | 1-10 years'),
])
pdf.h2('Credit Scoring')
pdf.bullet("Base score: 500")
pdf.bullet("+200 for full repayment ratio")
pdf.bullet("+5 per on-time payment")
pdf.bullet("-10 per late payment")
pdf.bullet("-150 per default")
pdf.bullet("Range: 300 to 850")
pdf.h2('Workflow')
pdf.bullet("User applies - amount, duration, frequency")
pdf.bullet("System checks credit score and exposure")
pdf.bullet("Admin approves or rejects")
pdf.bullet("Admin disburses - wallet credited")
pdf.bullet("Amortization schedule generated")
pdf.bullet("User repays - schedule updates")
pdf.bullet("Credit score recalculated after each payment")

# ── 14 ──
pdf.add_page()
pdf.h1('14. Referral System')
pdf.h2('Purpose')
pdf.bullet("Growth engine - users earn bonuses for inviting others")
pdf.h2('Mechanism')
pdf.bullet("Every user gets a unique referral code")
pdf.bullet("Share link: /register?ref=CODE")
pdf.bullet("Referrer earns 5% of referee's first investment")
pdf.bullet("Bonus credited directly to wallet")
pdf.bullet("Referral count and total earned shown on dashboard")
pdf.h2('Database Fields')
pdf.bullet("users.referral_code - unique per user")
pdf.bullet("users.referred_by - parent referrer ID")
pdf.bullet("transactions where type = 'referral_bonus'")

# ── 15 ──
pdf.add_page()
pdf.h1('15. Notifications Framework')
pdf.h2('Purpose')
pdf.body(
    "Keeps users informed about transactions, wins, loans, and events "
    "via in-app, email, and SMS channels."
)
pdf.h2('Channels')
pdf.bullet("In-app (database channel)")
pdf.bullet("Email (SMTP)")
pdf.bullet("SMS (future - Africa's Talking integration)")
pdf.h2('Events That Trigger Notifications')
pdf.bullet("Deposit confirmed")
pdf.bullet("Withdrawal processed")
pdf.bullet("Big win (lottery)")
pdf.bullet("Jackpot won")
pdf.bullet("Investment matured")
pdf.bullet("Loan approved/rejected")
pdf.bullet("Referral bonus earned")
pdf.bullet("KYC approved/rejected")
pdf.h2('Preferences')
pdf.bullet("User can opt out per channel")
pdf.bullet("Preferences stored in users.notification_preferences JSON")
pdf.bullet("Unread count cached and polled every 30s")

# ── 16 ──
pdf.add_page()
pdf.h1('16. KYC & Verification')
pdf.h2('Purpose')
pdf.body(
    "Know Your Customer - required for regulatory compliance and to prevent "
    "fraud. Users submit identity documents for verification."
)
pdf.h2('KYC Levels')
pdf.table([
    ('Basic', 'Email + phone verified'),
    ('Standard', 'National ID + selfie uploaded'),
    ('Enhanced', 'Proof of address + bank statement'),
    ('VIP', 'Full KYC + source of funds'),
])
pdf.h2('Database Tables')
pdf.table([
    ('kyc_documents', 'Uploaded documents'),
    ('users.kyc_status', 'Current status'),
    ('users.kyc_level', 'Verification tier'),
])
pdf.h2('Workflow')
pdf.bullet("User uploads ID document")
pdf.bullet("Admin reviews in /admin/kyc")
pdf.bullet("Approve - kyc_status = verified")
pdf.bullet("Reject - user notified with reason")
pdf.bullet("Higher levels unlock larger limits")

# ── 17 ──
pdf.add_page()
pdf.h1('17. User Profile & Settings')
pdf.h2('Purpose')
pdf.body(
    "Central page for user account management - personal info, password, "
    "notification preferences, bank accounts, and security settings."
)
pdf.h2('Sections')
pdf.bullet("Personal information (name, email, phone)")
pdf.bullet("Password update")
pdf.bullet("Two-factor authentication")
pdf.bullet("Notification preferences")
pdf.bullet("Bank account management")
pdf.bullet("Session management")
pdf.bullet("Account deletion")
pdf.h2('Security')
pdf.bullet("Password confirmation required for sensitive changes")
pdf.bullet("Session invalidation on password change")
pdf.bullet("Email notification on every major change")

# ── 18 ──
pdf.add_page()
pdf.h1('18. Admin Console')
pdf.h2('Purpose')
pdf.body(
    "Back-office for managing every aspect of the platform. Nine department "
    "admins each have dedicated sections tailored to their responsibilities."
)
pdf.h2('Admin Sections')
pdf.bullet("Dashboard - platform-wide stats and activity")
pdf.bullet("Users - list, edit, suspend, verify")
pdf.bullet("Deposits - verify, reject, upload statements")
pdf.bullet("Withdrawals - approve, process, reject")
pdf.bullet("KYC - review documents")
pdf.bullet("Investments - manage plans")
pdf.bullet("Machines - manage RX tiers")
pdf.bullet("Trading - monitor order book")
pdf.bullet("Lottery - games, symbols, jackpots, analytics")
pdf.bullet("Loans - approve, reject, disburse")
pdf.bullet("Reports - export CSV/PDF")
pdf.bullet("Settings - platform configuration")
pdf.h2('Access Control')
pdf.bullet("Admin middleware on every /admin route")
pdf.bullet("is_admin flag in users table")
pdf.bullet("Separate layout (admin.layouts.app)")

# ── 19 ──
pdf.add_page()
pdf.h1('19. Deployment & CI/CD')
pdf.h2('CI/CD Pipeline')
pdf.bullet("GitHub Actions runs on every push to spiral branch")
pdf.bullet("Workflow: Laravel CI - runs tests")
pdf.bullet("Workflow: Docker Build - builds multi-arch image")
pdf.bullet("Image pushed to GitHub Container Registry (GHCR)")
pdf.bullet("Both run in parallel, cancel outdated runs")
pdf.h2('Docker')
pdf.bullet("Multi-stage build - vendor, frontend, runtime")
pdf.bullet("Alpine base - small image size")
pdf.bullet("PHP 8.4 FPM with all extensions")
pdf.bullet("OPcache enabled for production")
pdf.bullet("Non-root user (www-data)")
pdf.bullet("Health check endpoint")
pdf.h2('Cloudflare Tunnel')
pdf.bullet("Public HTTPS URL without public IP")
pdf.bullet("Named tunnel for racksephnox.us.ci")
pdf.bullet("Auto-reconnect on network changes")
pdf.bullet("DDoS protection and WAF included")
pdf.bullet("Free SSL on edge")

# ── 20 ──
pdf.add_page()
pdf.h1('20. Security Architecture')
pdf.h2('Application Security')
pdf.bullet("CSRF protection on all forms")
pdf.bullet("SQL injection prevention via Eloquent")
pdf.bullet("XSS prevention via Blade escaping")
pdf.bullet("Password hashing with bcrypt 12 rounds")
pdf.bullet("Rate limiting on auth and sensitive routes")
pdf.bullet("Two-factor authentication schema ready")
pdf.bullet("Session encryption and regeneration")
pdf.h2('API Security')
pdf.bullet("Sanctum tokens for API authentication")
pdf.bullet("Token expiry and rotation")
pdf.bullet("Rate limiting per user and per IP")
pdf.bullet("CORS configured for known origins")
pdf.h2('Data Protection')
pdf.bullet("Sensitive fields encrypted at rest")
pdf.bullet("Database backups scheduled")
pdf.bullet("Audit log for every admin action")
pdf.bullet("GDPR-compliant data export/deletion")
pdf.h2('Financial Security')
pdf.bullet("All balance changes within transactions")
pdf.bullet("Row locking on wallet operations")
pdf.bullet("Duplicate transaction prevention")
pdf.bullet("Referral abuse detection")
pdf.bullet("Withdrawal approval required above threshold")
pdf.h2('Infrastructure Security')
pdf.bullet("Cloudflare DDoS protection")
pdf.bullet("SSL/TLS on all traffic")
pdf.bullet("Non-root container user")
pdf.bullet("Firewall rules on production server")

# ── CLOSING ──
pdf.add_page()
pdf.set_y(80)
pdf.set_font('Helvetica', 'B', 22)
pdf.set_text_color(184, 134, 11)
pdf.cell(0, 12, 'The Empire Documented', 0, 1, 'C')
pdf.ln(10)
pdf.set_font('Helvetica', 'I', 12)
pdf.set_text_color(80, 60, 20)
pdf.multi_cell(0, 8, sanitize(
    "Twenty frameworks. One unified vision. Racksephnox is not just software - "
    "it is an operating system for digital wealth in emerging markets."
), 0, 'C')
pdf.ln(20)
pdf.set_font('Helvetica', 'B', 11)
pdf.set_text_color(30, 30, 30)
pdf.cell(0, 8, 'Racksephnox', 0, 1, 'C')
pdf.set_font('Helvetica', '', 10)
pdf.cell(0, 6, sanitize('Infinite Spiral of Creation | 888 Hz | Golden Ratio Phi'), 0, 1, 'C')

output = '/root/racksephnox/Racksephnox_Framework_Documentation.pdf'
pdf.output(output)
print(f"PDF generated: {output}")
print(f"Pages: {pdf.page_no()}")
