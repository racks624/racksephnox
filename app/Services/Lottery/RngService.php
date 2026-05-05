<?php

namespace App\Services\Lottery;

use App\Models\LotterySymbol;
use Illuminate\Support\Facades\Cache;

class RngService
{
    protected $seeded = false;

    public function seed(int $seed): void
    {
        mt_srand($seed);
        $this->seeded = true;
    }

    public function reset(): void
    {
        mt_srand();
        $this->seeded = false;
    }

    public function getWeightedRandomSymbol(int $seed = null): LotterySymbol
    {
        if ($seed !== null) {
            $this->seed($seed);
        } elseif (!$this->seeded) {
            $this->seed(time());
        }

        $symbols = Cache::remember('lottery_symbols_weights', 3600, fn() => LotterySymbol::all());
        $totalWeight = $symbols->sum('weight');
        $rand = mt_rand(1, $totalWeight);
        $cumulative = 0;
        foreach ($symbols as $sym) {
            $cumulative += $sym->weight;
            if ($rand <= $cumulative) {
                $this->reset();
                return $sym;
            }
        }
        $this->reset();
        return $symbols->first();
    }
}
