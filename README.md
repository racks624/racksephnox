# Racksephnox – Kenyan Crypto Investment Platform

Racksephnox is an enterprise-grade cryptocurrency investment platform built with Laravel, supporting Kenyan Shillings (KES) and M-Pesa integration.

## Features
- User authentication with 2FA
- KYC verification (IdentityPass integration)
- M-Pesa deposits and withdrawals
- Multiple investment plans with daily interest
- Real-time crypto prices (CoinGecko)
- Admin dashboard
- Audit logging
- RESTful API for mobile apps

## Requirements
- PHP 8.2+
- MySQL 8.0+
- Redis (optional, for queues/cache)
- Composer

## Installation
1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure
4. Generate key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. Seed plans: `php artisan db:seed --class=InvestmentPlanSeeder`
7. Start server: `php artisan serve`

## Testing
Run `php artisan test`

## Deployment
Use the included Docker setup or deploy to a VPS (DigitalOcean, AWS). CI/CD is configured with GitHub Actions.

## License
Proprietary – all rights reserved.
