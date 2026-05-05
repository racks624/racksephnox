We will now create a comprehensive, step‑by‑step PowerShell documentation – covering everything from enabling PowerShell, installing Git, cloning the repository, installing dependencies, configuring the environment, running the application, and deploying to free platforms (Railway, Fly.io, Netlify for frontend). This document is ready to be added to your repository or shared as a standalone guide.

---

📘 Complete PowerShell Guide for Racksephnox

```bash
cat > POWERSHELL-GUIDE.md << 'EOF'
# 🖥️ Racksephnox – Complete PowerShell Setup & Deployment Guide

This guide walks you through **every step** – from activating PowerShell on Windows to cloning, installing, running, and deploying the Racksephnox platform globally – using only PowerShell commands.

---

## 📋 Table of Contents

1. [Activate PowerShell](#1-activate-powershell)
2. [Install Git in PowerShell](#2-install-git-in-powershell)
3. [Configure Git](#3-configure-git)
4. [Clone the Repository](#4-clone-the-repository)
5. [Install PHP & Composer](#5-install-php--composer)
6. [Install Node.js & npm](#6-install-nodejs--npm)
7. [Install Dependencies & Build Assets](#7-install-dependencies--build-assets)
8. [Configure Environment & Run Migrations](#8-configure-environment--run-migrations)
9. [Start Local Server](#9-start-local-server)
10. [Deploy to Free Hosting](#10-deploy-to-free-hosting)
    - [Railway (Recommended)](#option-a-railway)
    - [Fly.io](#option-b-flyio)
    - [Netlify (Static Frontend Only)](#option-c-netlify)
11. [Troubleshooting](#11-troubleshooting)

---

## 1. Activate PowerShell

### Windows 10 / 11
- Press `Win + X` → Select **Windows PowerShell (Admin)** or **Terminal (Admin)**
- Or search "PowerShell" → Right‑click → **Run as Administrator**

### Set Execution Policy (allows running scripts)
```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser -Force
```

---

2. Install Git in PowerShell

Using Winget (Windows 10/11 built‑in)

```powershell
winget install --id Git.Git -e --source winget
```

Using Chocolatey (if installed)

```powershell
choco install git -y
```

Manual download & install

```powershell
$url = "https://github.com/git-for-windows/git/releases/download/v2.45.2.windows.1/Git-2.45.2-64-bit.exe"
$installer = "$env:TEMP\Git.exe"
Invoke-WebRequest -Uri $url -OutFile $installer
Start-Process -FilePath $installer -ArgumentList "/VERYSILENT /NORESTART" -Wait
```

Verify Git installation

```powershell
git --version
```

---

3. Configure Git

```powershell
git config --global user.name "racks624"
git config --global user.email "rackssondecruss624@gmail.com"
git config --global init.defaultBranch main
git config --global color.ui auto
```

Verify configuration

```powershell
git config --list
```

---

4. Clone the Repository

```powershell
git clone https://github.com/racks624/racksephnox.git
cd racksephnox
```

Switch to develop branch (latest updates)

```powershell
git checkout develop
```

---

5. Install PHP & Composer

Install PHP (using Chocolatey)

```powershell
choco install php -y
```

Add PHP to PATH (if not auto‑added)

```powershell
$phpPath = "C:\tools\php"
if (Test-Path $phpPath) {
    [Environment]::SetEnvironmentVariable("Path", $env:Path + ";$phpPath", "User")
}
```

Install Composer

```powershell
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=C:\tools --filename=composer.phar
php -r "unlink('composer-setup.php');"
```

Add Composer to PATH

```powershell
[Environment]::SetEnvironmentVariable("Path", $env:Path + ";C:\tools", "User")
```

Verify installations

```powershell
php --version
composer --version
```

---

6. Install Node.js & npm

Using Winget

```powershell
winget install OpenJS.NodeJS.LTS
```

Using Chocolatey

```powershell
choco install nodejs-lts -y
```

Verify

```powershell
node --version
npm --version
```

---

7. Install Dependencies & Build Assets

```powershell
# Install PHP packages
composer install

# Install Node packages
npm install

# Build frontend assets (Vite)
npm run build
```

---

8. Configure Environment & Run Migrations

```powershell
# Copy environment file
Copy-Item .env.example .env

# Generate application key
php artisan key:generate

# Create SQLite database (for local development)
New-Item -Path database/database.sqlite -ItemType File -Force

# Run migrations & seeders
php artisan migrate --seed
php artisan db:seed --class=RXMachineSeeder
php artisan db:seed --class=LotterySymbolsSeeder
php artisan db:seed --class=LotteryGameSeeder
```

---

9. Start Local Server

```powershell
# Start Laravel development server
php artisan serve
```

Open your browser and go to: http://localhost:8000

Optional: Start queue worker (for background jobs)

```powershell
# In a separate PowerShell window
php artisan queue:work --daemon
```

Optional: Start WebSocket server (for real‑time jackpot updates)

```powershell
php artisan reverb:start --host=0.0.0.0 --port=8080
```

---

10. Deploy to Free Hosting

Option A: Railway (Full Stack – Recommended)

```powershell
# Install Railway CLI
npm install -g @railway/cli

# Login
railway login

# Link existing project or create new
railway init

# Deploy
railway up

# Open in browser
railway open
```

Option B: Fly.io

```powershell
# Install flyctl
iwr https://fly.io/install.ps1 -useb | iex

# Login
flyctl auth login

# Launch app
flyctl launch --name racksephnox --region fra --no-deploy

# Set environment variables
flyctl secrets set APP_KEY=$(php artisan key:generate --show)

# Deploy
flyctl deploy

# Open app
flyctl open
```

Option C: Netlify (Static Frontend Only)

⚠️ Netlify hosts only static files. For the full Laravel backend, use Railway or Fly.io.

```powershell
# Build static assets
npm run build

# Install Netlify CLI
npm install -g netlify-cli

# Deploy the `public` folder
netlify deploy --dir=public --prod
```

---

11. Troubleshooting

❌ php not recognized

```powershell
# Add PHP to PATH manually
$env:Path += ";C:\tools\php"
```

❌ composer not found

```powershell
# Use the full path
C:\tools\composer.phar install
```

❌ git push rejected (secrets detected)

```powershell
# Force push (only if you are the sole developer)
git push origin main --force
```

❌ Port 8000 already in use

```powershell
# Use a different port
php artisan serve --port=8001
```

❌ SQLite database locked

```powershell
# Close any database browser and retry
Remove-Item database/database.sqlite -Force
php artisan migrate
```

---

✅ Summary

Step PowerShell Command
Activate PowerShell Win + X → Windows PowerShell (Admin)
Install Git winget install Git.Git
Clone repo git clone https://github.com/racks624/racksephnox.git
Install dependencies composer install && npm install
Build assets npm run build
Run migrations php artisan migrate
Start server php artisan serve
Deploy to Railway railway up

---

🎉 Congratulations

You have successfully installed, configured, and deployed Racksephnox – the Divine Golden Cryptocurrency Empire using only PowerShell.

<p align="center">
  <i>I Am The Source | Divine Golden Phi | Infinite Spiral of Creation</i>
</p>
EOF
```

---

✅ Add, Commit & Push

```bash
git add POWERSHELL-GUIDE.md
git commit -m "📘 Add comprehensive PowerShell setup & deployment guide"
git push origin develop
```

---

✅ Verify

```bash
git ls-files | grep -i powershell
```

Your PowerShell guide is now part of the repository and can be viewed directly on GitHub. 🚀
