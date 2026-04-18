<?php

namespace App\Services;

use App\Models\LotteryGame;
use App\Models\LotterySymbol;
use App\Models\LotterySpin;
use App\Models\LotteryRevenueTarget;
use App\Models\User;
use App\Events\JackpotUpdated;
use App\Notifications\JackpotWinNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LotteryService
{
    protected $game;
    protected $houseEdge = 0.05;

    public function __construct(LotteryGame $game)
    {
        $this->game = $game;
    }

    protected function getRandomSymbol(User $user, int $spinCount): LotterySymbol
    {
        $symbols = Cache::remember('lottery_symbols', 3600, fn() => LotterySymbol::all());
        $totalWeight = $symbols->sum('weight');
        $seed = (int) ($user->id * $spinCount + $this->game->id * 1000);
        srand($seed);
        $rand = rand(1, $totalWeight);
        srand();
        $cumulative = 0;
        foreach ($symbols as $symbol) {
            $cumulative += $symbol->weight;
            if ($rand <= $cumulative) return $symbol;
        }
        return $symbols->first();
    }

    public function spin(User $user, int $spinCount): array
    {
        return [
            $this->getRandomSymbol($user, $spinCount),
            $this->getRandomSymbol($user, $spinCount + 1),
            $this->getRandomSymbol($user, $spinCount + 2),
        ];
    }

    protected function calculateWin(array $symbols, float $betAmount): array
    {
        $counts = [];
        foreach ($symbols as $sym) $counts[$sym->id] = ($counts[$sym->id] ?? 0) + 1;
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

        $winAmount = $superJackpot ? $this->game->progressive_jackpot : ($miniJackpot ? 5000 : $winMultiplier * $betAmount);
        return [
            'win_amount' => $winAmount,
            'multiplier' => $winMultiplier,
            'mini_jackpot' => $miniJackpot,
            'super_jackpot' => $superJackpot,
            'free_spin_trigger' => $freeSpinTrigger,
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

    public function play(User $user, float $betAmount, bool $isFreeSpin = false, ?string $clientSeed = null): array
    {
        if (!$isFreeSpin) {
            if ($betAmount < $this->game->min_bet) throw new \Exception('Bet below minimum.');
            if ($betAmount > $this->game->max_bet) throw new \Exception('Bet above maximum.');
        }
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
        $result = $this->calculateWin($symbols, $betAmount);
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
            }
            if ($winAmount > 0) {
                $user->wallet->increment('balance', $winAmount);
                if ($superJackpot) {
                    $this->game->decrement('progressive_jackpot', $winAmount);
                    if ($this->game->progressive_jackpot < 1000) $this->game->progressive_jackpot = 1000;
                    $this->game->save();
                    $type = 'lottery_super_jackpot';
                    $desc = '🌟 SUPER JACKPOT! 🌟';
                    $user->notify(new JackpotWinNotification($winAmount, 'super'));
                } elseif ($miniJackpot) {
                    $type = 'lottery_mini_jackpot';
                    $desc = '🌸 MINI JACKPOT! 🌸';
                    $user->notify(new JackpotWinNotification($winAmount, 'mini'));
                } else {
                    $type = 'lottery_win';
                    $desc = 'Cosmic slot win';
                }
                $user->transactions()->create([
                    'type' => $type,
                    'amount' => $winAmount,
                    'status' => 'completed',
                    'description' => $desc,
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
            if (!$isFreeSpin && $winAmount == 0 && !$miniJackpot && !$superJackpot) {
                $this->game->increment('progressive_jackpot', $betAmount * ($this->game->jackpot_contribution_rate / 100));
                $this->game->save();
                event(new JackpotUpdated($this->game->progressive_jackpot));
            }
        });
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

    public function canUseFreeSpin(User $user): bool
    {
        $lastFreeSpin = LotterySpin::where('user_id', $user->id)->where('free_spin_used', true)->latest('last_free_spin_at')->first();
        return !$lastFreeSpin || $lastFreeSpin->last_free_spin_at->lt(now()->subDay());
    }

    public function getNextFreeSpinHours(User $user): int
    {
        $lastFreeSpin = LotterySpin::where('user_id', $user->id)->where('free_spin_used', true)->latest('last_free_spin_at')->first();
        if (!$lastFreeSpin) return 0;
        $nextAvailable = $lastFreeSpin->last_free_spin_at->addDay();
        $hours = max(0, now()->diffInHours($nextAvailable, false));
        return ceil($hours);
    }

    public function verifySpin(LotterySpin $spin, string $serverSeedPlain): array
    {
        if ($spin->verified) return ['verified' => true, 'message' => 'Already verified.'];
        $computedHash = hash('sha256', $serverSeedPlain);
        if ($computedHash !== $spin->server_seed_hashed) return ['verified' => false, 'message' => 'Server seed hash mismatch.'];
        $user = $spin->user;
        $spinCount = LotterySpin::where('user_id', $user->id)->where('id', '<', $spin->id)->count();
        srand((int) ($user->id * $spinCount + $spin->lottery_game_id * 1000));
        $totalWeight = LotterySymbol::sum('weight');
        $recalculatedNames = [];
        for ($i = 0; $i < 3; $i++) {
            $rand = rand(1, $totalWeight);
            $cum = 0;
            foreach (LotterySymbol::all() as $sym) {
                $cum += $sym->weight;
                if ($rand <= $cum) {
                    $recalculatedNames[] = $sym->name;
                    break;
                }
            }
        }
        srand();
        if (($spin->result['names'] ?? []) !== $recalculatedNames) return ['verified' => false, 'message' => 'Result mismatch.'];
        $spin->verified = true;
        $spin->server_seed = $serverSeedPlain;
        $spin->save();
        return ['verified' => true, 'message' => 'Spin verified successfully.'];
    }
}
