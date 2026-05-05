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

    protected $fillable = [
        'user_id', 'machine_id', 'vip_level', 'amount', 'daily_profit', 'total_return',
        'start_date', 'end_date', 'status', 'profit_credited', 'last_profit_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'daily_profit' => 'decimal:2',
        'total_return' => 'decimal:2',
        'profit_credited' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'last_profit_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE && $this->end_date->isFuture();
    }

    public function daysElapsed()
    {
        if (!$this->start_date) return 0;
        return max(0, Carbon::now()->diffInDays($this->start_date, false));
    }

    public function daysRemaining()
    {
        if (!$this->end_date) return 0;
        $remaining = Carbon::now()->diffInDays($this->end_date, false);
        return max(0, $remaining);
    }

    public function progressPercentage()
    {
        $totalDays = $this->machine->duration_days;
        if ($totalDays <= 0) return 0;
        $elapsed = $this->daysElapsed();
        return min(100, round(($elapsed / $totalDays) * 100, 2));
    }

    public function currentProfit()
    {
        return round($this->profit_credited, 2);
    }
}
