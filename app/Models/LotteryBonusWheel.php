<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotteryBonusWheel extends Model
{
    protected $table = 'lottery_bonus_wheel';
    protected $fillable = ['name', 'segments', 'is_active'];
    protected $casts = ['segments' => 'array', 'is_active' => 'boolean'];

    public function spins()
    {
        return $this->hasMany(LotteryBonusWheelSpin::class, 'wheel_id');
    }
}
