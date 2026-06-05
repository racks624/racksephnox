<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Investment extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id', 'plan_id', 'machine_id', 'amount', 'daily_profit',
        'total_projected_profit', 'remaining_days', 'status', 'start_date',
        'end_date', 'last_accrued_at', 'auto_reinvest', 'compound_type',
        'early_withdrawal_penalty', 'max_cycles', 'current_cycle'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'daily_profit' => 'decimal:2',
        'total_projected_profit' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'last_accrued_at' => 'datetime',
        'auto_reinvest' => 'boolean',
        'early_withdrawal_penalty' => 'decimal:2',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function plan() { return $this->belongsTo(InvestmentPlan::class); }
    public function machine() { return $this->belongsTo(Machine::class); }

    public function isActive() { return $this->status === self::STATUS_ACTIVE && $this->end_date->isFuture(); }

    public function daysRemaining()
    {
        if (!$this->end_date) return 0;
        $remaining = Carbon::now()->diffInDays($this->end_date, false);
        return max(0, $remaining);
    }

    public function progressPercentage()
    {
        $totalDays = $this->plan->duration_days;
        if ($totalDays <= 0) return 0;
        $elapsed = $totalDays - $this->remaining_days;
        return min(100, round(($elapsed / $totalDays) * 100, 2));
    }
}
