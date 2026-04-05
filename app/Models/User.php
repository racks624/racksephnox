<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password',
        'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at',
        'is_admin', 'kyc_level', 'is_verified',
        'referral_code', 'referred_by', 'onboarding_completed',
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'is_admin' => 'boolean',
        'is_verified' => 'boolean',
        'onboarding_completed' => 'boolean',
        'notification_preferences' => 'array',
    ];

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function mpesaTransactions()
    {
        return $this->hasMany(MpesaTransaction::class);
    }

    public function kycDocuments()
    {
        return $this->hasMany(KycDocument::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function machineInvestments()
    {
        return $this->hasMany(MachineInvestment::class);
    }

    public function tradingAccount()
    {
        return $this->hasOne(TradingAccount::class);
    }

    public function tradeOrders()
    {
        return $this->hasMany(TradeOrder::class);
    }

    public function depositRequests()
    {
        return $this->hasMany(DepositRequest::class);
    }

    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(UserBankAccount::class);
    }

    protected static function booted()
    {
        static::created(function ($user) {
            // Create wallet for new user
            $user->wallet()->create(['balance' => 0]);

            // Generate referral code
            $user->referral_code = strtoupper(substr(md5($user->id . $user->email), 0, 8));
            $user->save();

            // Registration bonus (KES 60)
            $user->wallet->credit(60, 'Welcome bonus');
        });
    }
}
