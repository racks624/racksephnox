<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositRequest extends Model
{
    use HasFactory;

    protected $table = 'deposit_requests';

    protected $fillable = [
        'user_id', 'amount', 'method', 'reference', 'phone',
        'transaction_code', 'paybill_number', 'account_number',
        'status', 'verification_status',
        'approved_by', 'verified_by', 'verified_at',
        'approved_at', 'rejection_reason',
        'verification_notes', 'match_confidence', 'user_submitted_at',
        'notes',
    ];

    protected $casts = [
        'amount'             => 'float',
        'approved_at'        => 'datetime',
        'verified_at'        => 'datetime',
        'user_submitted_at'  => 'datetime',
        'match_confidence'   => 'integer',
    ];

    /* ─── Relationships ─── */
    public function user()         { return $this->belongsTo(User::class); }
    public function approver()     { return $this->belongsTo(User::class, 'approved_by'); }
    public function verifier()     { return $this->belongsTo(User::class, 'verified_by'); }
    public function statementEntry()
    {
        return $this->hasOne(MpesaStatementEntry::class, 'matched_deposit_id');
    }

    /* ─── Scopes ─── */
    public function scopePending($q)         { return $q->where('status', 'pending'); }
    public function scopeAwaitingVerify($q)  { return $q->where('verification_status', 'pending'); }
    public function scopeVerified($q)        { return $q->where('verification_status', 'verified'); }
    public function scopeRejected($q)        { return $q->where('verification_status', 'rejected'); }
    public function scopeForUser($q, $id)    { return $q->where('user_id', $id); }

    /* ─── Helpers ─── */
    public function getIsAwaitingVerificationAttribute(): bool
    {
        return $this->verification_status === 'pending' && $this->status === 'pending';
    }

    public function getIsVerifiedAttribute(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function getConfidenceLabelAttribute(): string
    {
        return match (true) {
            $this->match_confidence >= 95 => 'Exact Match',
            $this->match_confidence >= 75 => 'High Confidence',
            $this->match_confidence >= 50 => 'Partial Match',
            $this->match_confidence > 0   => 'Low Confidence',
            default                       => 'Unverified',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->verification_status) {
            'verified'  => 'text-green-400',
            'rejected'  => 'text-red-400',
            'auto'      => 'text-blue-400',
            'pending'   => 'text-yellow-400',
            default     => 'text-ivory/60',
        };
    }
}
