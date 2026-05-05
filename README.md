# 🚀 Racksephnox – Divine Golden Cryptocurrency Empire

![Laravel](https://img.shields.io/badge/Laravel-13.x-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.4-blue?logo=php)
![Vite](https://img.shields.io/badge/Vite-5.x-purple?logo=vite)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?logo=tailwind-css)
![License](https://img.shields.io/badge/License-MIT-green)

**Racksephnox** is a **Global‑Market Commercial‑Enterprise Grade** cryptocurrency investment, trading, and lottery platform. It features a golden‑themed UI, real‑time Bitcoin trading, RX Machine investments (RX0–RX6), provably fair lottery slots, social copy trading, M‑Pesa integration, and full admin controls.

🔗 **Live Demo:** *Coming soon*  
📦 **Repository:** [github.com/racks624/racksephnox](https://github.com/racks624/racksephnox)

---

## ✨ Features

### 💰 Investment – RX Machine Series
- 7 machines (RX0 Origin → RX6 Infinity)
- VIP 1–3 levels with Fibonacci‑based amounts
- Fixed **88% total ROI** over 14 days (~12.6% daily)
- Early withdrawal with penalty (configurable)
- Referral bonus (5%)

### ₿ Bitcoin Trading Engine
- Real‑time order book (bids/asks)
- Market, limit, stop orders
- Take‑profit & stop‑loss
- Copy trading & leaderboard
- Trading streak bonus (8 trades in 24h)

### 🎰 Cosmic Lottery Slots
- Canvas slot machine with SVG/emoji fallback
- Progressive jackpot (mini & super)
- Daily free spin, bonus wheel
- Daily missions, weekly tournaments
- Provably fair verification
- Volatility, RTP segments, loss limits, cooldown

### 👑 Admin Dashboard
- Full CRUD: users, machines, KYC, deposits, withdrawals
- Lottery analytics & symbol weight editor
- Investment plan editor
- Real‑time stats & charts

### 🔌 Integrations
- **M‑Pesa** (STK push, B2C, callbacks)
- **WebSockets** (Laravel Reverb) for live jackpot updates
- **Mail** (SMTP/log)
- **Queue** (database/redis)

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 13.x, PHP 8.4 |
| Frontend | Vite, Alpine.js, TailwindCSS, Chart.js |
| Database | SQLite (dev) / MySQL, PostgreSQL (prod) |
| Queue | Database / Redis |
| WebSockets | Laravel Reverb |
| Payments | M‑Pesa API |
| Hosting | Railway, Fly.io, Docker |

---

## 📦 Installation

### Prerequisites
- PHP ≥ 8.4
- Composer
- Node.js ≥ 18 & npm
- Git
- SQLite (or MySQL)

### Clone & Setup

```bash
# Clone the repository
git clone https://github.com/racks624/racksephnox.git
cd racksephnox

# Install PHP dependencies
composer install

# Install Node dependencies & compile assets
npm install
npm run build

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations (SQLite auto‑creates database/database.sqlite)
php artisan migrate --seed

# Run seeder for machines, lottery symbols, etc.
php artisan db:seed --class=RXMachineSeeder
php artisan db:seed --class=LotterySymbolsSeeder
php artisan db:seed --class=LotteryGameSeeder

# Start development server
php artisan serve
☁️ Deployment

Deploy to Railway (Recommended)

```bash
# Install Railway CLI
npm install -g @railway/cli

# Login
railway login

# Link or create project
railway init

# Set environment variables (APP_KEY, DB, etc.)
railway variables set APP_KEY=$(php artisan key:generate --show)

# Deploy
railway up
```

Deploy to Fly.io

```bash
# Install flyctl
curl -L https://fly.io/install.sh | sh

# Login
flyctl auth login

# Launch
flyctl launch --name racksephnox --region fra

# Set secrets
flyctl secrets set APP_KEY=$(php artisan key:generate --show)

# Deploy
flyctl deploy
```

Docker (Self‑Hosted)

```bash
docker build -t racksephnox .
docker run -p 8080:9000 -v $(pwd):/var/www racksephnox
```

---

🧪 Testing

```bash
php artisan test
```

---

📂 Project Structure (Key Folders)

```
app/
├── Console/Commands/          # Cron jobs (profit accrual, tournaments)
├── Http/Controllers/          # Web & API controllers
├── Models/                    # Eloquent models
├── Services/                  # Business logic (TradingEngine, LotteryService)
├── Notifications/             # Email & database notifications
database/
├── migrations/                # All database schemas
├── seeders/                   # RX machines, lottery symbols, etc.
resources/
├── js/lottery/                # Canvas slot machine, sounds, SVG symbols
├── views/                     # Blade templates (golden theme)
routes/
├── web.php, api.php, lottery.php, admin.php
```

---

🔧 Environment Variables (.env)

Variable Description
APP_NAME Application name
APP_ENV local, production
APP_KEY Run php artisan key:generate
APP_DEBUG true in dev, false in prod
DB_CONNECTION sqlite, mysql
MPESA_* M‑Pesa sandbox keys
REVERB_* WebSocket configuration

---

🧑‍💻 Contributing

1. Fork the repository
2. Create a feature branch (git checkout -b feature/amazing)
3. Commit changes (git commit -m 'Add amazing feature')
4. Push (git push origin feature/amazing)
5. Open a Pull Request

---

📄 License

MIT © Racksephnox

---

🙏 Acknowledgements

· Laravel Community
· TailwindCSS
· Alpine.js
· Chart.js
· FontAwesome

---

📞 Support

For issues, please open a GitHub Issue.
For business inquiries: support@racksephnox.com

---

<p align="center">
  <i>I Am The Source | Divine Golden Phi | Infinite Spiral of Creation</i>
</p>
