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

    protected $table = 'machine_investments';

    protected $fillable = [
        'user_id', 'machine_id', 'vip_level', 'amount', 'daily_profit', 'total_return',
        'start_date', 'end_date', 'status', 'profit_credited', 'last_profit_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'daily_profit' => 'decimal:2',
        'total_return' => 'decimal:2',
        'profit_credited' => 'decimal:2',
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
}
