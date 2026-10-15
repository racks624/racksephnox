<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpesaStatementEntry extends Model
{
    use HasFactory;

    protected $table = 'mpesa_statement_entries';

    protected $fillable = [
        'statement_id', 'receipt_number', 'transaction_code',
        'transaction_time', 'amount', 'payer_name', 'payer_phone',
        'account_number', 'paybill_number', 'match_status',
        'matched_deposit_id', 'matched_at', 'raw_row',
    ];

    protected $casts = [
        'transaction_time' => 'datetime',
        'amount'           => 'float',
        'matched_at'       => 'datetime',
        'raw_row'          => 'array',
    ];

    public function statement() { return $this->belongsTo(MpesaStatement::class, 'statement_id'); }
    public function deposit()   { return $this->belongsTo(DepositRequest::class, 'matched_deposit_id'); }

    public function scopeUnmatched($q) { return $q->where('match_status', 'unmatched'); }
    public function scopeMatched($q)   { return $q->where('match_status', 'matched'); }
}
