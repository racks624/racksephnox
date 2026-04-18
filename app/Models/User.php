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
        'name', 'email', 'password', 'phone', 'referral_code', 'referred_by',
        'is_admin', 'is_verified', 'kyc_status', 'kyc_level', 'onboarding_completed',
        'is_vip', 'has_promo', 'promo_expires_at', 'free_spins_available',
        'last_activity_at', 'preferred_currency',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
        'is_verified' => 'boolean',
        'onboarding_completed' => 'boolean',
        'is_vip' => 'boolean',
        'has_promo' => 'boolean',
        'promo_expires_at' => 'datetime',
        'free_spins_available' => 'integer',
    ];

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function tradingAccount()
    {
        return $this->hasOne(TradingAccount::class);
    }

    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    public function machineInvestments()
    {
        return $this->hasMany(MachineInvestment::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function lotterySpins()
    {
        return $this->hasMany(LotterySpin::class);
    }

    public function lotteryTournamentEntries()
    {
        return $this->hasMany(LotteryTournamentEntry::class);
    }

    public function lotteryUserMissions()
    {
        return $this->hasMany(LotteryUserMission::class);
    }

    public function lotteryBonusWheelSpins()
    {
        return $this->hasMany(LotteryBonusWheelSpin::class);
    }
}
