<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LotteryBonusWheelSpin extends Model
{
    protected $table = 'lottery_bonus_wheel_spins';
    protected $fillable = ['user_id', 'wheel_id', 'prize_type', 'prize_value', 'spun_at'];
    protected $casts = ['spun_at' => 'datetime'];
    public function user() { return $this->belongsTo(User::class); }
    public function wheel() { return $this->belongsTo(LotteryBonusWheel::class, 'wheel_id'); }
}
