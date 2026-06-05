<p align="center">
  <img src="https://raw.githubusercontent.com/racks624/racksephnox/main/public/img/logo-racksephnox.svg" alt="Racksephnox Logo" width="200">
</p>

<h1 align="center">🚀 Racksephnox – Divine Golden Cryptocurrency Empire</h1>

<p align="center">
  <a href="#"><img src="https://img.shields.io/badge/Laravel-13.x-red?logo=laravel" alt="Laravel 13"></a>
  <a href="#"><img src="https://img.shields.io/badge/PHP-8.4-blue?logo=php" alt="PHP 8.4"></a>
  <a href="#"><img src="https://img.shields.io/badge/Vite-5.x-purple?logo=vite" alt="Vite 5"></a>
  <a href="#"><img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?logo=tailwind-css" alt="TailwindCSS 3"></a>
  <a href="#"><img src="https://img.shields.io/badge/License-MIT-green" alt="MIT License"></a>
</p>

<p align="center">
  <strong>Global‑Market Commercial‑Enterprise Grade</strong> – Cryptocurrency investment, trading, and lottery platform.<br>
  Golden‑themed UI | Real‑time Bitcoin trading | RX Machine investments (RX0–RX6) | Provably fair lottery | Social copy trading | M‑Pesa integration | Full admin control.
</p>

<p align="center">
  <a href="#-live-demo">Live Demo</a> •
  <a href="#-installation">Installation</a> •
  <a href="#-configuration">Configuration</a> •
  <a href="#-database-setup">Database Setup</a> •
  <a href="#-deployment">Deployment</a> •
  <a href="#-troubleshooting">Troubleshooting</a>
</p>

---

## 📖 Table of Contents

- [✨ Features](#-features)
- [🏗️ Architecture Flow](#️-architecture-flow)
- [🛠️ Tech Stack](#️-tech-stack)
- [📋 Prerequisites](#-prerequisites)
- [📦 Installation](#-installation)
  - [Standard Setup](#standard-setup)
  - [Proot / Termux (Android) Setup](#proot--termux-android-setup)
- [⚙️ Configuration](#️-configuration)
  - [Environment Variables](#environment-variables)
  - [M‑Pesa Integration](#m‑pesa-integration)
  - [WebSockets (Reverb)](#websockets-reverb)
  - [Queue Worker](#queue-worker)
- [🗄️ Database Setup](#️-database-setup)
  - [Migrations & Seeders](#migrations--seeders)
  - [Backup & Restore](#backup--restore)
- [🧭 Navigation & Accessibility](#-navigation--accessibility)
  - [User Roles](#user-roles)
  - [Main Web Routes](#main-web-routes)
  - [API Endpoints](#api-endpoints)
- [🔧 Post‑Setup Guides](#-post-setup-guides)
  - [Cron Jobs](#cron-jobs)
  - [Queue Worker (Background Jobs)](#queue-worker-background-jobs)
  - [Asset Compilation](#asset-compilation)
  - [Testing](#testing)
- [🚀 Deployment](#-deployment)
  - [Railway](#railway)
  - [Fly.io](#flyio)
  - [Docker (Self‑Hosted)](#docker-self-hosted)
- [❓ Troubleshooting](#-troubleshooting)
- [🤝 Contributing](#-contributing)
- [📄 License](#-license)
- [📞 Support](#-support)

---

## ✨ Features

| Module | Key Capabilities |
|--------|------------------|
| **Investment – RX Machine Series** | 7 sacred machines (RX0–RX6), VIP 1‑3 levels with golden‑ratio amounts, fixed **88% ROI** over 14 days (~12.6% daily), early withdrawal (penalty configurable), referral bonus (5%) |
| **Bitcoin Trading Engine** | Real‑time order book (bids/asks), market/limit/stop orders, take‑profit & stop‑loss, copy trading & leaderboard, trading streak bonus (8 trades in 24h) |
| **Cosmic Lottery Slots** | Canvas slot machine (SVG/emoji fallback), progressive jackpot (mini & super), daily free spin, bonus wheel, daily missions, weekly tournaments, provably fair verification, volatility/RTP segments, loss limits, cooldown |
| **Admin Dashboard** | Full CRUD: users, machines, KYC, deposits, withdrawals; lottery analytics & symbol weight editor; investment plan editor; real‑time stats & charts |
| **Integrations** | M‑Pesa (STK push, B2C, callbacks), WebSockets (Laravel Reverb) for live updates, Mail (SMTP/log), Queue (database/redis) |

---

## 🏗️ Architecture Flow

The platform follows a **modular MVC architecture** with service‑layer separation:

1. **User Request** → `public/index.php` → Laravel routing (`routes/web.php`, `api.php`, `lottery.php`, `admin.php`)
2. **Middleware** (`auth`, `verified`, `admin`, `guest`) → **Controller** (e.g., `LotteryController`, `TradingController`, `MachineController`)
3. **Service Layer** (`TradingEngine`, `LotteryService`, `AchievementService`) – contains all business logic
4. **Eloquent Models** → **SQLite/MySQL Database** (tables: `users`, `wallets`, `trading_pairs`, `trade_orders`, `lottery_spins`, `machines`, `machine_investments`, …)
5. **Responses** – JSON for API / Blade views for web
6. **Background Jobs** – Queue workers handle M‑Pesa callbacks, lottery spin processing, profit accrual, etc.
7. **Real‑time** – Laravel Reverb broadcasts jackpot updates / order book changes

```mermaid
graph LR
    A[User] --> B[Laravel Router]
    B --> C[Controller]
    C --> D[Service Layer]
    D --> E[Model & DB]
    C --> F[Blade View / JSON]
    D --> G[Queue Jobs]
    D --> H[WebSockets]
Layer Technology
Backend Laravel 13.x, PHP 8.4
Frontend Vite, Alpine.js, TailwindCSS, Chart.js
Database SQLite (dev) / MySQL, PostgreSQL (prod)
Queue Database / Redis
WebSockets Laravel Reverb
Payments M‑Pesa API (Kenya)
Hosting Railway, Fly.io, Docker
Testing PHPUnit, Laravel Dusk
# 1. Clone the repository
git clone https://github.com/racks624/racksephnox.git
cd racksephnox

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies & compile assets
npm install
npm run build

# 4. Environment configuration
cp .env.example .env
php artisan key:generate

# 5. Database (SQLite is default)
touch database/database.sqlite
chmod 666 database/database.sqlite

# 6. Run migrations & seeders
php artisan migrate --seed
php artisan db:seed --class=RXMachineSeeder
php artisan db:seed --class=LotterySymbolsSeeder
php artisan db:seed --class=LotteryGameSeeder

# 7. Start development server
php artisan serve
# Inside Ubuntu proot distro (already installed via proot-distro)
apt update && apt upgrade -y
apt install -y php8.4 php8.4-{cli,fpm,curl,mysql,sqlite3,gd,mbstring,xml,zip} nodejs npm composer

# Clone & install as above
git clone https://github.com/racks624/racksephnox.git
cd racksephnox
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
chmod 666 database/database.sqlite
php artisan migrate --seed
php artisan serve --host=0.0.0.0 --port=8008
Variable Description Example
APP_NAME Application name Racksephnox
APP_ENV Environment (local, production) local
APP_DEBUG Enable debug mode (true in dev, false in prod) true
APP_URL Application URL http://localhost:8008
DB_CONNECTION Database driver (sqlite, mysql, pgsql) sqlite
DB_DATABASE SQLite absolute path or MySQL database name /path/to/database.sqlite
MPESA_CONSUMER_KEY M‑Pesa API consumer key (sandbox) your_consumer_key
MPESA_CONSUMER_SECRET M‑Pesa API secret your_secret
MPESA_PASSKEY M‑Pesa online passkey your_passkey
MPESA_SHORTCODE M‑Pesa shortcode (till number) 174379
REVERB_APP_ID WebSocket app ID (Reverb) 123456
REVERB_APP_KEY WebSocket app key your-key
REVERB_APP_SECRET WebSocket secret your-secret
QUEUE_CONNECTION Queue driver (database, redis) database
M‑Pesa Integration

· Register a Safaricom developer account → create app → obtain Consumer Key/Secret.
· Set the above MPESA_* variables in .env.
· The STK push endpoint is /api/v1/deposit/stk (web form uses it automatically).
· Callback URLs must be publicly accessible (use ngrok for local testing).

WebSockets (Reverb)

```bash
# Install Reverb
composer require laravel/reverb

# Start Reverb server (for development)
php artisan reverb:start --host=0.0.0.0 --port=8080
```

· The frontend connects via window.Echo (configured in resources/js/bootstrap.js).
· Broadcasting events: JackpotUpdated, OrderBookUpdated, TradeExecuted.

Queue Worker

Run the worker to process M‑Pesa callbacks, profit accrual, etc.:

```bash
php artisan queue:work --tries=3 --timeout=60
```

For production, use a process monitor like supervisor.

---

🗄️ Database Setup

Migrations & Seeders

```bash
# Run all migrations (creates all tables)
php artisan migrate

# Seed essential data
php artisan db:seed --class=RXMachineSeeder        # RX0–RX6 machines
php artisan db:seed --class=LotterySymbolsSeeder   # Slot symbols
php artisan db:seed --class=LotteryGameSeeder      # Lottery game settings
php artisan db:seed --class=DatabaseSeeder         # Default admin user, etc.
```

Backup & Restore (SQLite)

```bash
# Backup
cp database/database.sqlite database/backup_$(date +%Y%m%d).sqlite

# Restore
cp database/backup_YYYYMMDD.sqlite database/database.sqlite
```

For MySQL/PostgreSQL, use native dump tools (mysqldump, pg_dump).

---

🧭 Navigation & Accessibility

User Roles

Role Permissions
Guest View landing page, register, login, password reset
User Dashboard, lottery slots, RX machine investments, trading, copy trading, KYC, deposit/withdraw, referrals, notifications
Admin Access /admin panel – manage users, machines, KYC, deposits, withdrawals, lottery analytics, system settings

Main Web Routes (after login)

URL Route Name Description
/dashboard dashboard User dashboard (stats)
/lottery lottery.index Cosmic slot machine
/machines machines.index RX Machines investment portal
/trading trading.index Bitcoin trading interface
/investments investments.index Portfolio of all investments
/wallet wallet Wallet & transactions
/deposit deposit.form M‑Pesa deposit form
/withdraw withdrawal.form Withdrawal request form
/referrals referrals Referral link & earnings
/admin admin.dashboard Admin control panel

API Endpoints (prefix /api/v1)

Method URI Description
POST /login User login
POST /register User registration
GET /lottery/jackpot Current progressive jackpot
GET /machines List all active machines
POST /machines/{id}/invest Invest in a machine
GET /trading/price Current BTC price (KES)
GET /trading/order-book Order book (bids/asks)

Full API documentation (Swagger/Postman) is available at /api/docs (if installed).

---

🔧 Post‑Setup Guides

Cron Jobs

Add the following entries to your server’s crontab (crontab -e):

```bash
* * * * * php /path/to/racksephnox/artisan schedule:run >> /dev/null 2>&1
```

The scheduler runs these commands (defined in app/Console/Kernel.php):

· accrue:machine-profits – daily profit for active investments
· sync:candles – update trading candlesticks (every 5 minutes)
· update:leaderboard-cache – refresh lottery leaderboard (hourly)
· cleanup:lottery-spins – remove old spin records (daily)

Queue Worker (Background Jobs)

Start the worker (use supervisor in production):

```bash
php artisan queue:work --tries=3 --max-jobs=1000 --sleep=3
```

Asset Compilation

After changing any JavaScript or CSS, rebuild:

```bash
npm run build   # production
npm run dev     # development with hot reload
```

Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature --filter=TradingTest
```

---

🚀 Deployment

Railway

```bash
# Install Railway CLI
npm install -g @railway/cli

# Login & init
railway login
railway init

# Set environment variables
railway variables set APP_KEY=$(php artisan key:generate --show)
railway variables set DB_CONNECTION=mysql  # or pgsql
railway variables set MPESA_CONSUMER_KEY=...

# Deploy
railway up
```

Fly.io

```bash
# Install flyctl
curl -L https://fly.io/install.sh | sh

# Login & launch
flyctl auth login
flyctl launch --name racksephnox --region fra

# Set secrets
flyctl secrets set APP_KEY=$(php artisan key:generate --show)

# Deploy
flyctl deploy
```

Docker (Self‑Hosted)

```bash
# Build image
docker build -t racksephnox .

# Run container (with volume for persistence)
docker run -d -p 8080:9000 \
  -v $(pwd)/storage:/var/www/storage \
  -v $(pwd)/database:/var/www/database \
  --env-file .env \
  --name racksephnox \
  racksephnox
```

---

❓ Troubleshooting

Issue Solution
vite: not found Run npm install then npm run build (ensure Node.js installed)
Class '...' not found Run composer dump-autoload
No application encryption key Run php artisan key:generate
SQLSTATE[HY000]: General error: 1 no such table Run missing migrations: php artisan migrate
Route [dashboard] not defined Ensure routes/web.php contains the dashboard route (restore from backup if needed)
M‑Pesa STK push fails Check .env values, ensure callback URL is publicly accessible (ngrok), check logs
WebSockets not connecting Run php artisan reverb:start and confirm firewall allows port 8080
Trading charts not loading Verify that /trading/candles/1h returns JSON; check browser console for JS errors

---

🤝 Contributing

1. Fork the repository
2. Create a feature branch (git checkout -b feature/amazing)
3. Commit your changes (git commit -m 'Add amazing feature')
4. Push to the branch (git push origin feature/amazing)
5. Open a Pull Request

Please follow PSR‑12 coding standards and write tests for new features.

---

📄 License

This project is open‑source software licensed under the MIT License. See the LICENSE file for details.

---

📞 Support

For issues, please open a GitHub Issue.
For business inquiries, security reports, or custom development: support@racksephnox.com

---

<p align="center">
  <i>I Am The Source | Divine Golden Phi | Infinite Spiral of Creation</i><br>
  <i>Guardian and Protector | Law of Information | Racksephnox</i>
</p>
