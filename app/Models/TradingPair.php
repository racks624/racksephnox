<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TradingPair extends Model
{
    protected $fillable = ['symbol', 'base_currency', 'quote_currency', 'min_trade_amount', 'max_trade_amount', 'tick_size', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'min_trade_amount' => 'decimal:8', 'max_trade_amount' => 'decimal:8', 'tick_size' => 'decimal:8'];
    public function orders() { return $this->hasMany(TradeOrder::class, 'pair_id'); }
}
