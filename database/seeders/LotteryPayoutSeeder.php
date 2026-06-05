<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\LotteryGame;
use App\Models\LotterySymbol;
class LotteryPayoutSeeder extends Seeder
{
    public function run()
    {
        $game = LotteryGame::where('is_active', true)->first();
        if (!$game) return;
        $symbols = LotterySymbol::all();
        $multipliers = [
            'divine_sword' => 10,
            'divine_bell' => 8,
            'golden_flower' => 0, // triggers free spins, not direct win
            'frequency_8888' => 12,
            'frequency_7777' => 10,
            'taurus' => 6,
            'tree_of_life' => 5,
            'divine_star' => 50, // super jackpot symbol
        ];
        foreach ($symbols as $symbol) {
            if (isset($multipliers[$symbol->name])) {
                $game->symbols()->attach($symbol->id, [
                    'count' => 3,
                    'payout_multiplier' => $multipliers[$symbol->name],
                ]);
            }
        }
    }
}
