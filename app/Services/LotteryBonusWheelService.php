<?php
namespace App\Services;
use App\Models\User;
class LotteryBonusWheelService {
    public function canSpin(User $user) { return false; }
}
