<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotteryAchievement extends Model
{
    protected $fillable = ['name', 'description', 'icon', 'condition_type', 'condition_value', 'reward_free_spins'];
}
