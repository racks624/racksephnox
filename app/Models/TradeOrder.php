<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TradeOrder extends Model
{
    protected $table = 'trade_orders';
    protected $fillable = [
        'user_id', 'pair_id', 'side', 'order_type', 'amount_btc', 'filled_amount',
        'limit_price', 'stop_price', 'take_profit_price', 'stop_loss_price',
        'price_per_btc', 'filled_kes', 'status', 'time_in_force', 'expires_at'
    ];
    protected $casts = [
        'amount_btc' => 'decimal:8', 'filled_amount' => 'decimal:8',
        'limit_price' => 'decimal:2', 'stop_price' => 'decimal:2',
        'take_profit_price' => 'decimal:2', 'stop_loss_price' => 'decimal:2',
        'price_per_btc' => 'decimal:2', 'filled_kes' => 'decimal:2',
        'expires_at' => 'datetime'
    ];
    public function user() { return $this->belongsTo(User::class); }
    public function pair() { return $this->belongsTo(TradingPair::class); }
}
