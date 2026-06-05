<?php
namespace App\Services;
use App\Models\LotteryGame;
use App\Models\User;
class LotteryService {
    protected $game;
    public function __construct(LotteryGame $game) { $this->game = $game; }
    public function canUseFreeSpin(User $user) { return true; }
    public function getNextFreeSpinHours(User $user) { return 0; }
    public function play(User $user, $bet, $isFree = false, $clientSeed = null) {
        return ['symbols' => [], 'win_amount' => 0, 'net_change' => 0, 'mini_jackpot' => false, 'super_jackpot' => false, 'free_spin_trigger' => false, 'progressive_jackpot' => 0, 'nonce' => 0, 'client_seed' => '', 'server_seed_hashed' => ''];
    }
    public function verifySpin($spin, $serverSeed) { return ['valid' => false]; }
}
