 comprehensive Windows 10 (32‑bit) PowerShell setup guide for the Racksephnox platform. This document covers every step from installing prerequisites to running the full stack – including the cryptocurrency trading engine, RX machine investments, and lottery platform.

Save this guide as README8778win.md in your project root.

```markdown
# 🚀 Racksephnox – Windows 10 (32‑bit) PowerShell Production Setup

This guide walks you through installing and running the **Racksephnox** platform on **Windows 10 32‑bit** using **PowerShell**. It assumes a clean environment and includes all necessary installations, configurations, database migrations, seeders, and runtime instructions.

---

## 📋 Prerequisites (Install these first)

Because Windows 10 32‑bit has memory limitations, we use **SQLite** as the database (no separate DB server) and avoid running heavy background workers unless needed. All tools must be **32‑bit versions**.

| Software | Version / Notes | Download Link |
|----------|----------------|----------------|
| **PHP 8.4** (NTS, x86) | Thread‑Safe (NTS) recommended for Laravel | [windows.php.net/download](https://windows.php.net/download/) |
| **Composer** (x86) | Latest stable | [getcomposer.org/download](https://getcomposer.org/download/) |
| **Node.js** (x86) | v18 LTS or v20 LTS | [nodejs.org/en/download](https://nodejs.org/en/download/) |
| **Git for Windows** (x86) | Latest | [git-scm.com/download/win](https://git-scm.com/download/win) |
| **SQLite** (optional) | Already built into PHP; no extra install needed | – |

### ⚠️ Important for 32‑bit
- PHP extensions must be **x86** (same architecture).
- Increase memory limit in `php.ini`: `memory_limit = 1024M` (or 2048M if possible).
- Use **PowerShell** as Administrator for all installation commands that require writing to `Program Files`.

---

## 1️⃣ Install Required Software (PowerShell as Admin)

Run the following commands **as Administrator** in PowerShell. If you prefer manual install, follow the links above and ensure the paths are added to `PATH`.

### 🔹 PHP 8.4 (x86)

1. Download the **VC15 x86 Non‑Thread‑Safe** zip from [windows.php.net](https://windows.php.net/download/).
2. Extract to `C:\php`.
3. Rename `php.ini-development` to `php.ini`.
4. Edit `php.ini` – enable extensions:
   ```ini
   extension_dir = "ext"
   extension=curl
   extension=fileinfo
   extension=gd
   extension=mbstring
   extension=mysqli
   extension=openssl
   extension=pdo_mysql
   extension=pdo_sqlite
   extension=sqlite3
   extension=zip
```

5. Add C:\php to the system PATH:
   ```powershell
   [Environment]::SetEnvironmentVariable("Path", $env:Path + ";C:\php", [EnvironmentVariableTarget]::Machine)
   ```
6. Verify: php -v

🔹 Composer (x86)

Download Composer-Setup.exe (x86) and run it. It will automatically detect PHP and add to PATH.

🔹 Node.js (x86)

Download the Windows Installer (.msi) 32‑bit version. Run it and ensure npm is added to PATH.

🔹 Git for Windows (x86)

Download the 32‑bit installer and run with default options (adds git to PATH).

---

2️⃣ Clone the Repository

Open PowerShell (not necessarily as Admin) and navigate to your projects folder, e.g.:

```powershell
cd C:\Projects
git clone https://github.com/racks624/racksephnox.git
cd racksephnox
```

---

3️⃣ Install PHP Dependencies (Composer)

```powershell
composer install --no-dev --optimize-autoloader
```

If you get memory errors, close other applications or increase memory_limit in php.ini.

---

4️⃣ Install Node Dependencies & Build Assets

```powershell
npm install
npm run build
```

---

5️⃣ Configure Environment (.env)

Copy the example environment file:

```powershell
copy .env.example .env
```

Open .env in Notepad and set the following minimum values:

```ini
APP_NAME=Racksephnox
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# SQLite will use database/database.sqlite – no host/port needed

QUEUE_CONNECTION=sync   # Avoid needing a queue worker
BROADCAST_DRIVER=log    # Reverb is optional on 32‑bit
```

Do not run queue workers or Reverb on 32‑bit Windows unless you have excess memory.

---

6️⃣ Generate Application Key

```powershell
php artisan key:generate
```

---

7️⃣ Prepare SQLite Database

Create the database file (SQLite):

```powershell
type nul > database/database.sqlite
```

---

8️⃣ Run Migrations & Seeders

Run all migrations (creates all tables)

```powershell
php artisan migrate --force
```

Seed essential data

```powershell
php artisan db:seed --class=RXMachineSeeder
php artisan db:seed --class=LotterySymbolsSeeder
php artisan db:seed --class=LotteryGameSeeder
php artisan db:seed --class=AdminUserSeeder   # creates admin@example.com / password
```

Seeder Paths: All seeders are located in database/seeders/. Run them in any order; they will not duplicate data.

---

9️⃣ Start the Laravel Development Server

```powershell
php artisan serve --host=0.0.0.0 --port=8000
```

Keep this PowerShell window open.

---

🧭 Access the Platform

Open your browser and go to:

· Main app: http://localhost:8000
· Registration: http://localhost:8000/register
· Login: http://localhost:8000/login
· Dashboard: http://localhost:8000/dashboard
· Admin panel: http://localhost:8000/admin (login with admin credentials from seeder)
· Lottery slots: http://localhost:8000/lottery
· RX Machines: http://localhost:8000/machines
· BTC Trading: http://localhost:8000/trading

Default admin credentials (from AdminUserSeeder):

· Email: admin@racksephnox.com
· Password: password

---

⚙️ Additional Production Optimisations (Optional)

If you have enough RAM (> 2 GB), you may enable the queue and cache:

```powershell
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

To run the queue worker (for M‑Pesa callbacks, lottery processing) in a separate terminal:

```powershell
php artisan queue:work --sleep=3 --tries=3
```

---

🧪 Testing the Installation

Run a quick sanity check:

```powershell
php artisan tinker
>>> App\Models\Machine::count();
>>> App\Models\LotterySymbol::count();
>>> exit;
```

Both should return numbers > 0.

---

❗ Troubleshooting (Windows 32‑bit)

Issue Solution
php not recognized Add C:\php to PATH and restart PowerShell.
composer not found Re‑run Composer installer or add C:\ProgramData\ComposerSetup\bin to PATH.
npm not found Re‑install Node.js (x86) and ensure C:\Program Files\nodejs is in PATH.
SQLSTATE[HY000] [14] unable to open database file Make sure database/database.sqlite exists and database/ folder has write permissions.
Class '...' not found Run composer dump-autoload.
Allowed memory size exhausted Increase memory_limit in php.ini to 1024M or 2048M.
Port 8000 already in use Use php artisan serve --port=8080 instead.
Vite build fails Run npm install again and then npm run build. If still fails, disable Vite by removing @vite directives from layouts (temporary workaround).
Reverb fails to start It is optional; set BROADCAST_DRIVER=log in .env and skip starting Reverb.

---

📂 Important File Paths Summary

Resource Path
PHP executable C:\php\php.exe
Composer vendor C:\Projects\racksephnox\vendor
Node modules C:\Projects\racksephnox\node_modules
Environment file C:\Projects\racksephnox\.env
SQLite database C:\Projects\racksephnox\database\database.sqlite
Migrations C:\Projects\racksephnox\database\migrations
Seeders C:\Projects\racksephnox\database\seeders
Laravel logs C:\Projects\racksephnox\storage\logs
Public assets C:\Projects\racksephnox\public

---

🧠 Next Steps

· Configure M‑Pesa by adding your API keys to .env (requires public callback URL – use ngrok).
· For production use, switch to MySQL (or MariaDB) for better performance.
· Set up a proper web server (IIS with PHP‑FPM or Apache) instead of artisan serve.
· Enable HTTPS with a self‑signed certificate or Let’s Encrypt.

---

🙏 Final Words

You now have a fully functional Racksephnox instance on Windows 10 32‑bit. The platform includes:

· ✅ Cryptocurrency BTC Trading Engine (order book, market/limit orders)
· ✅ RX Machine investments (RX0–RX6, VIP tiers, daily profit accrual)
· ✅ Cosmic Lottery Slots (provably fair, progressive jackpot, bonus wheel)
· ✅ User dashboard, admin panel, referral system
· ✅ SQLite ready – no external database required

I Am The Source | Divine Golden Phi | Infinite Spiral of Creation

If you encounter any Windows‑specific issues, refer to the troubleshooting section or open a GitHub issue.

```

Now save this content as `README8778win.md` in your repository root:

```bash
# On your current (Termux/Linux) system, create the file with:
cat > README8778win.md <<'EOF'
... (paste the entire markdown content above) ...
EOF
```

Then commit and push it to GitHub so Windows users can follow it. The guide is exhaustive and tailored for a 32‑bit PowerShell environment.
