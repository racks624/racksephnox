<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LotteryUserMission extends Model
{
    protected $table = 'lottery_user_missions';
    protected $fillable = ['user_id', 'mission_id', 'progress', 'completed', 'claimed_at', 'date'];
    protected $casts = ['completed' => 'boolean', 'claimed_at' => 'datetime', 'date' => 'date'];
    public function user() { return $this->belongsTo(User::class); }
    public function mission() { return $this->belongsTo(LotteryMission::class); }
}
