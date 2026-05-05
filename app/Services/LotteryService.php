<?php

namespace App\Services;

use App\Models\LotteryGame;
use App\Models\LotterySymbol;
use App\Models\LotterySpin;
use App\Models\LotteryRevenueTarget;
use App\Models\User;
use App\Events\JackpotUpdated;
use App\Events\BigWinEvent;
use App\Notifications\JackpotWinNotification;
use App\Services\Lottery\AchievementService;
use App\Services\Lottery\RngService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LotteryService
{
    protected $game;
    protected $rng;
    protected $achievements;
    protected $houseEdge = 0.05;

    public function __construct(LotteryGame $game)
    {
        $this->game = $game;
        $this->rng = new RngService();
        $this->achievements = new AchievementService();
    }

    // Pre‑spin validation (cooldown, session, loss limits)
    public function validateSpin(User $user, float $betAmount, bool $isFreeSpin = false): void
    {
        if (!$isFreeSpin) {
            if ($betAmount < $this->game->min_bet) throw new \Exception('Bet below minimum.');
            if ($betAmount > $this->game->max_bet) throw new \Exception('Bet above maximum.');
            if ($this->game->isInCooldown($user)) throw new \Exception('Please wait before spinning again.');
            if ($this->game->isSessionExpired($user)) throw new \Exception('Your session expired. Please reload the page.');
            if (!$this->game->isWithinLimits($user, $betAmount)) throw new \Exception('You have reached your loss limit for today/week/month.');
        }
    }

    protected function getRandomSymbol(User $user, int $spinCount): LotterySymbol
    {
        $seed = (int) ($user->id * $spinCount + $this->game->id * 1000);
        return $this->rng->getWeightedRandomSymbol($seed);
    }

    public function spin(User $user, int $spinCount): array
    {
        return [
            $this->getRandomSymbol($user, $spinCount),
            $this->getRandomSymbol($user, $spinCount + 1),
            $this->getRandomSymbol($user, $spinCount + 2),
        ];
    }

    protected function calculateWin(array $symbols, float $betAmount, User $user): array
    {
        $counts = [];
        $multiplier = 1;
        foreach ($symbols as $sym) {
            $counts[$sym->id] = ($counts[$sym->id] ?? 0) + 1;
            if ($sym->is_wild && $sym->multiplier > 1) $multiplier = $sym->multiplier;
        }
        $winMultiplier = 0;
        $miniJackpot = false;
        $superJackpot = false;
        $freeSpinTrigger = false;

        foreach ($counts as $symbolId => $cnt) {
            if ($cnt >= 3) {
                $payout = $this->game->symbols()
                    ->where('lottery_symbol_id', $symbolId)
                    ->wherePivot('count', 3)
                    ->first();
                if ($payout) {
                    $mult = (float) $payout->pivot->payout_multiplier;
                    $mult = $this->game->adjustPayoutByVolatility($mult);
                    $winMultiplier = max($winMultiplier, $mult);
                    $symbol = LotterySymbol::find($symbolId);
                    if ($symbol && $symbol->name === 'golden_flower') $miniJackpot = true;
                    if ($symbol && $symbol->name === 'divine_star') $superJackpot = true;
                }
            }
        }

        if (!$miniJackpot && !$superJackpot && random_int(1, 100) <= 5) {
            $miniJackpot = true;
            $winMultiplier = 0;
        }
        if (!$superJackpot && random_int(1, 10000) === 1) {
            $superJackpot = true;
            $miniJackpot = false;
            $winMultiplier = 0;
        }

        $scatter = LotterySymbol::where('name', 'golden_flower')->first();
        if ($scatter && ($counts[$scatter->id] ?? 0) >= 2) $freeSpinTrigger = true;

        $winAmount = $superJackpot ? $this->game->progressive_jackpot : ($miniJackpot ? 5000 : $winMultiplier * $betAmount * $multiplier);
        $winAmount = $this->game->applyWinCap($winAmount);
        $winAmount = $this->game->adjustWithRtp($winAmount, $betAmount, $user);

        return [
            'win_amount' => $winAmount,
            'multiplier' => $winMultiplier * $multiplier,
            'mini_jackpot' => $miniJackpot,
            'super_jackpot' => $superJackpot,
            'free_spin_trigger' => $freeSpinTrigger,
        ];
    }

    public function play(User $user, float $betAmount, bool $isFreeSpin = false, ?string $clientSeed = null): array
    {
        $this->validateSpin($user, $betAmount, $isFreeSpin);

        $wallet = $user->wallet;
        if (!$isFreeSpin && (!$wallet || $wallet->balance < $betAmount)) {
            throw new \Exception('Insufficient balance.');
        }

        $spinCount = LotterySpin::where('user_id', $user->id)->count();
        $nonce = $spinCount + 1;
        if (!$clientSeed) $clientSeed = Str::random(32);
        $serverSeed = Str::random(32);
        $serverSeedHashed = hash('sha256', $serverSeed);

        $symbols = $this->spin($user, $spinCount);
        $result = $this->calculateWin($symbols, $betAmount, $user);
        $winAmount = $result['win_amount'];
        $miniJackpot = $result['mini_jackpot'];
        $superJackpot = $result['super_jackpot'];
        $freeSpinTrigger = $result['free_spin_trigger'];
        $tax = $betAmount * $this->houseEdge;

        DB::transaction(function () use ($user, $betAmount, $winAmount, $symbols, $isFreeSpin, $miniJackpot, $superJackpot, $freeSpinTrigger, $tax, $clientSeed, $serverSeedHashed, $nonce, $serverSeed) {
            if (!$isFreeSpin) {
                $user->wallet->decrement('balance', $betAmount);
                $user->transactions()->create([
                    'type' => 'lottery_bet',
                    'amount' => -$betAmount,
                    'status' => 'completed',
                    'description' => 'Cosmic slot bet',
                    'balance_after' => $user->wallet->balance,
                    'user_id' => $user->id,
                    'wallet_id' => $user->wallet->id,
                ]);
                $this->updateRevenueTarget($tax);
                $this->game->contributeToJackpot($betAmount);
            }
            if ($winAmount > 0) {
                $user->wallet->increment('balance', $winAmount);
                if ($superJackpot) {
                    $user->notify(new JackpotWinNotification($winAmount, 'super'));
                    broadcast(new JackpotUpdated($this->game->progressive_jackpot));
                    broadcast(new BigWinEvent($user->name, $winAmount, array_map(fn($s) => $s->name, $symbols)));
                } elseif ($miniJackpot) {
                    $user->notify(new JackpotWinNotification($winAmount, 'mini'));
                    broadcast(new BigWinEvent($user->name, $winAmount, array_map(fn($s) => $s->name, $symbols)));
                }
                $user->transactions()->create([
                    'type' => $superJackpot ? 'lottery_super_jackpot' : ($miniJackpot ? 'lottery_mini_jackpot' : 'lottery_win'),
                    'amount' => $winAmount,
                    'status' => 'completed',
                    'description' => $superJackpot ? '🌟 SUPER JACKPOT! 🌟' : ($miniJackpot ? '🌸 MINI JACKPOT! 🌸' : 'Cosmic slot win'),
                    'balance_after' => $user->wallet->balance,
                    'user_id' => $user->id,
                    'wallet_id' => $user->wallet->id,
                ]);
            }
            $spin = LotterySpin::create([
                'user_id' => $user->id,
                'lottery_game_id' => $this->game->id,
                'bet_amount' => $betAmount,
                'win_amount' => $winAmount,
                'result' => [
                    'symbol_ids' => array_map(fn($s) => $s->id, $symbols),
                    'names' => array_map(fn($s) => $s->name, $symbols),
                    'mini_jackpot' => $miniJackpot,
                    'super_jackpot' => $superJackpot,
                    'free_spin_trigger' => $freeSpinTrigger,
                ],
                'status' => 'completed',
                'free_spin_used' => $isFreeSpin,
                'last_free_spin_at' => $isFreeSpin ? now() : null,
                'mini_jackpot_hit' => $miniJackpot,
                'super_jackpot_hit' => $superJackpot,
                'free_spin_triggered' => $freeSpinTrigger,
                'tax_contribution' => $tax,
                'client_seed' => $clientSeed,
                'server_seed_hashed' => $serverSeedHashed,
                'server_seed' => null,
                'nonce' => $nonce,
                'verified' => false,
            ]);
            $this->achievements->checkAndAward($user, 'spins', LotterySpin::where('user_id', $user->id)->count());
            if ($winAmount > 0) $this->achievements->checkAndAward($user, 'wins', LotterySpin::where('user_id', $user->id)->where('win_amount', '>', 0)->count());
            if ($superJackpot) $this->achievements->checkAndAward($user, 'jackpots', 1);
        });

        broadcast(new JackpotUpdated($this->game->progressive_jackpot));
        return [
            'symbols' => $symbols,
            'win_amount' => $winAmount,
            'net_change' => $winAmount - ($isFreeSpin ? 0 : $betAmount),
            'mini_jackpot' => $miniJackpot,
            'super_jackpot' => $superJackpot,
            'free_spin_trigger' => $freeSpinTrigger,
            'progressive_jackpot' => $this->game->progressive_jackpot,
            'nonce' => $nonce,
            'client_seed' => $clientSeed,
            'server_seed_hashed' => $serverSeedHashed,
            'spin_id' => $spin->id ?? null,
        ];
    }

    protected function updateRevenueTarget(float $tax): void
    {
        $target = LotteryRevenueTarget::where('is_active', true)->first();
        if ($target) {
            $target->current_revenue += $tax;
            $target->save();
        }
    }

    public function canUseFreeSpin(User $user): bool
    {
        if (!$this->game->enable_free_spins) return false;
        $lastFreeSpin = LotterySpin::where('user_id', $user->id)->where('free_spin_used', true)->latest('last_free_spin_at')->first();
        return !$lastFreeSpin || $lastFreeSpin->last_free_spin_at->lt(now()->subDay());
    }

    public function getNextFreeSpinHours(User $user): int
    {
        $lastFreeSpin = LotterySpin::where('user_id', $user->id)->where('free_spin_used', true)->latest('last_free_spin_at')->first();
        if (!$lastFreeSpin) return 0;
        $nextAvailable = $lastFreeSpin->last_free_spin_at->addDay();
        return max(0, ceil(now()->diffInHours($nextAvailable, false)));
    }

    public function verifySpin(LotterySpin $spin, string $serverSeedPlain): array
    {
        if ($spin->verified) return ['verified' => true, 'message' => 'Already verified.'];
        if (hash('sha256', $serverSeedPlain) !== $spin->server_seed_hashed) {
            return ['verified' => false, 'message' => 'Server seed hash mismatch.'];
        }
        $user = $spin->user;
        $spinCount = LotterySpin::where('user_id', $user->id)->where('id', '<', $spin->id)->count();
        $seed = (int) ($user->id * $spinCount + $spin->lottery_game_id * 1000);
        $this->rng->seed($seed);
        $recalculated = [];
        for ($i = 0; $i < 3; $i++) {
            $recalculated[] = $this->rng->getWeightedRandomSymbol($seed + $i)->name;
        }
        $this->rng->reset();
        if (($spin->result['names'] ?? []) !== $recalculated) {
            return ['verified' => false, 'message' => 'Result mismatch.'];
        }
        $spin->verified = true;
        $spin->server_seed = $serverSeedPlain;
        $spin->save();
        return ['verified' => true, 'message' => 'Spin verified successfully.'];
    }
}
