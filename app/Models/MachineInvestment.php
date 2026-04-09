<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MachineInvestment extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Wealth Tax Constants (8888 Hz)
    const DAILY_TAX_RATE = 0.0088;
    const WEEKLY_TAX_RATE = 0.08888;
    const MONTHLY_TAX_RATE = 0.88888888;
    const YEARLY_TAX_RATE = 0.8888888888888;

    protected $table = 'machine_investments';

    protected $fillable = [
        'user_id', 'machine_id', 'vip_level', 'amount', 'daily_profit', 'total_return',
        'start_date', 'end_date', 'status', 'profit_credited', 'last_profit_date',
        'wealth_tax_daily', 'wealth_tax_weekly', 'wealth_tax_monthly', 'wealth_tax_yearly'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'daily_profit' => 'decimal:2',
        'total_return' => 'decimal:2',
        'profit_credited' => 'decimal:2',
        'wealth_tax_daily' => 'decimal:2',
        'wealth_tax_weekly' => 'decimal:2',
        'wealth_tax_monthly' => 'decimal:2',
        'wealth_tax_yearly' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'last_profit_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE && now()->lt($this->end_date);
    }

    public function isMatured()
    {
        return now()->gte($this->end_date) && $this->status === self::STATUS_ACTIVE;
    }

    public function daysElapsed()
    {
        return now()->diffInDays($this->start_date);
    }

    public function daysRemaining()
    {
        if ($this->isMatured()) return 0;
        return now()->diffInDays($this->end_date, false);
    }

    public function progressPercentage()
    {
        return round(($this->daysElapsed() / $this->machine->duration_days) * 100, 2);
    }

    public function currentProfit()
    {
        $daysElapsed = min($this->daysElapsed(), $this->machine->duration_days);
        return $this->daily_profit * $daysElapsed;
    }

    /**
     * Accrue daily profit with 8888 Hz Wealth Tax
     */
    public function accrueDailyProfit(): ?float
    {
        if (!$this->isActive()) return null;
        
        $today = now()->startOfDay();
        $lastProfitDate = $this->last_profit_date ? Carbon::parse($this->last_profit_date)->startOfDay() : $this->start_date->startOfDay();
        
        if ($today->lte($lastProfitDate)) return null;
        
        $daysToAccrue = min($today->diffInDays($lastProfitDate), $this->daysRemaining());
        
        if ($daysToAccrue <= 0) return null;
        
        $grossProfit = $this->daily_profit * $daysToAccrue;
        
        // Calculate 8888 Hz Wealth Tax
        $wealthTax = round($grossProfit * self::DAILY_TAX_RATE, 2);
        $netProfit = $grossProfit - $wealthTax;
        
        // Credit net profit to user's wallet
        $this->user->wallet->increment('balance', $netProfit);
        
        // Record transaction
        $this->user->transactions()->create([
            'type' => 'machine_interest',
            'amount' => $netProfit,
            'status' => 'completed',
            'description' => "Daily profit from {$this->machine->name} - VIP {$this->vip_level} (Gross: KES {$grossProfit}, Wealth Tax: KES {$wealthTax})",
            'balance_after' => $this->user->wallet->balance,
        ]);
        
        // Update investment record
        $this->profit_credited = ($this->profit_credited ?? 0) + $netProfit;
        $this->wealth_tax_daily = ($this->wealth_tax_daily ?? 0) + $wealthTax;
        $this->last_profit_date = $today;
        $this->save();
        
        // Check if investment is now matured
        if ($this->isMatured()) {
            $this->completeInvestment();
        }
        
        return $netProfit;
    }

    /**
     * Complete investment with final wealth tax calculation
     */
    public function completeInvestment(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) return false;
        
        $remainingProfit = $this->total_return - $this->amount - ($this->profit_credited ?? 0);
        if ($remainingProfit > 0) {
            $wealthTax = round($remainingProfit * self::DAILY_TAX_RATE, 2);
            $netRemaining = $remainingProfit - $wealthTax;
            
            $this->user->wallet->increment('balance', $netRemaining);
            $this->user->transactions()->create([
                'type' => 'machine_maturity',
                'amount' => $netRemaining,
                'status' => 'completed',
                'description' => "Maturity bonus from {$this->machine->name} - VIP {$this->vip_level} (Wealth Tax: KES {$wealthTax})",
                'balance_after' => $this->user->wallet->balance,
            ]);
            $this->profit_credited = ($this->profit_credited ?? 0) + $netRemaining;
            $this->wealth_tax_daily = ($this->wealth_tax_daily ?? 0) + $wealthTax;
        }
        
        $this->status = self::STATUS_COMPLETED;
        $this->save();
        
        return true;
    }
}
