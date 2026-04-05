<?php

namespace App\Models;

use App\Events\WalletBalanceUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'balance', 'locked_balance'];

    protected $casts = [
        'balance' => 'decimal:2',
        'locked_balance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function credit($amount, $description, $type = 'credit')
    {
        return \DB::transaction(function () use ($amount, $description, $type) {
            $this->increment('balance', $amount);
            return $this->transactions()->create([
                'type' => $type,
                'amount' => $amount,
                'balance_after' => $this->balance,
                'description' => $description,
            ]);
        });
    }

    public function debit($amount, $description, $type = 'debit')
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient balance');
        }
        return \DB::transaction(function () use ($amount, $description, $type) {
            $this->decrement('balance', $amount);
            return $this->transactions()->create([
                'type' => $type,
                'amount' => -$amount,
                'balance_after' => $this->balance,
                'description' => $description,
            ]);
        });
    }

    protected static function booted()
    {
        static::updated(function ($wallet) {
            if ($wallet->isDirty('balance') || $wallet->isDirty('locked_balance')) {
                broadcast(new WalletBalanceUpdated($wallet));
            }
        });
    }
}
