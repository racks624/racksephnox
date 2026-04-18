<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotteryRevenueTarget extends Model
{
    protected $fillable = [
        'target_amount', 'current_revenue', 'start_date', 'end_date', 'is_active'
    ];
    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_revenue' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];
}
