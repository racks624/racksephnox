<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'username', 'bio', 'avatar', 'is_public', 'allow_copy_trading',
        'total_pnl', 'win_rate', 'total_trades', 'followers_count', 'following_count'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'allow_copy_trading' => 'boolean',
        'total_pnl' => 'decimal:2',
        'win_rate' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followed_traders', 'trader_id', 'follower_id')
                    ->withPivot('copy_ratio', 'auto_copy', 'max_copy_amount')
                    ->withTimestamps();
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'followed_traders', 'follower_id', 'trader_id')
                    ->withPivot('copy_ratio', 'auto_copy', 'max_copy_amount')
                    ->withTimestamps();
    }
}
