<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LotteryMission extends Model
{
    protected $table = 'lottery_missions';
    protected $fillable = ['name', 'description', 'type', 'target', 'reward_type', 'reward_amount', 'reward_free_spins', 'is_daily'];
    protected $casts = ['reward_amount' => 'decimal:2', 'is_daily' => 'boolean'];
    public function userMissions() { return $this->hasMany(LotteryUserMission::class); }
}
