<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineInvestment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'machine_id', 'vip_level', 'amount', 'daily_profit', 'total_return',
        'start_date', 'end_date', 'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'daily_profit' => 'decimal:2',
        'total_return' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

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
}
