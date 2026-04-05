<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpesaTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'transaction_type', 'transaction_id',
        'amount', 'phone', 'reference', 'description',
        'status', 'mpesa_receipt_number', 'transaction_date',
        'raw_callback_data',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime',
        'raw_callback_data' => 'array',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
