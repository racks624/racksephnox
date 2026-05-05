<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TradingCandle extends Model
{
    protected $fillable = ['pair_id', 'interval', 'open_time', 'close_time', 'open', 'high', 'low', 'close', 'volume'];
    protected $casts = ['open_time' => 'datetime', 'close_time' => 'datetime', 'open' => 'decimal:8', 'high' => 'decimal:8', 'low' => 'decimal:8', 'close' => 'decimal:8', 'volume' => 'decimal:8'];
}
