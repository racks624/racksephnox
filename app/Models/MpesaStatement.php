<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpesaStatement extends Model
{
    use HasFactory;

    protected $table = 'mpesa_statements';

    protected $fillable = [
        'file_name', 'file_path', 'total_rows', 'matched_rows', 'unmatched_rows',
        'total_amount', 'statement_from', 'statement_to', 'uploaded_by', 'status',
    ];

    protected $casts = [
        'total_amount'    => 'float',
        'total_rows'      => 'integer',
        'matched_rows'    => 'integer',
        'unmatched_rows'  => 'integer',
        'statement_from'  => 'date',
        'statement_to'    => 'date',
    ];

    public function entries()  { return $this->hasMany(MpesaStatementEntry::class, 'statement_id'); }
    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function getMatchRateAttribute(): float
    {
        if ($this->total_rows <= 0) return 0.0;
        return round(($this->matched_rows / $this->total_rows) * 100, 2);
    }
}
