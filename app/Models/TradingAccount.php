<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingAccount extends Model
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

    public function credit($amount, $description = null)
    {
        return \DB::transaction(function () use ($amount) {
            $this->increment('balance', $amount);
            return $this->user->transactions()->create([
                'type' => 'trading_deposit',
                'amount' => $amount,
                'balance_after' => $this->balance,
                'description' => $description ?? 'Funds transferred to trading account',
            ]);
        });
    }

    public function debit($amount, $description = null)
    {
        if ($this->balance < $amount) {
            throw new \Exception('Insufficient trading balance');
        }
        return \DB::transaction(function () use ($amount) {
            $this->decrement('balance', $amount);
            return $this->user->transactions()->create([
                'type' => 'trading_withdrawal',
                'amount' => -$amount,
                'balance_after' => $this->balance,
                'description' => $description ?? 'Funds withdrawn from trading account',
            ]);
        });
    }
}
