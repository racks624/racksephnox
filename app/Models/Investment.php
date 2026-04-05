<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'plan_id', 'amount', 'daily_profit', 'total_projected_profit',
        'remaining_days', 'status', 'start_date', 'end_date', 'last_accrued_at',
        'auto_reinvest', 'compound_type', 'early_withdrawal_penalty',
        'early_withdrawn_at', 'early_withdrawn_amount', 'current_cycle', 'max_cycles'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'daily_profit' => 'decimal:2',
        'total_projected_profit' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'last_accrued_at' => 'datetime',
        'early_withdrawn_at' => 'datetime',
        'auto_reinvest' => 'boolean',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EARLY_WITHDRAWN = 'early_withdrawn';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(InvestmentPlan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Calculate early withdrawal amount (after penalty).
     */
    public function calculateEarlyWithdrawalAmount()
    {
        $penaltyPercent = $this->early_withdrawal_penalty ?? $this->plan->early_withdrawal_penalty ?? 5;
        $penaltyAmount = $this->amount * ($penaltyPercent / 100);
        return round($this->amount - $penaltyAmount, 2);
    }

    /**
     * Process early withdrawal.
     */
    public function earlyWithdraw()
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            throw new \Exception('Investment is not active');
        }
        $refund = $this->calculateEarlyWithdrawalAmount();
        \DB::transaction(function () use ($refund) {
            $this->user->wallet->credit($refund, 'Early withdrawal from ' . $this->plan->name);
            $this->update([
                'status' => self::STATUS_EARLY_WITHDRAWN,
                'early_withdrawn_at' => now(),
                'early_withdrawn_amount' => $refund,
            ]);
        });
        return $refund;
    }
}
