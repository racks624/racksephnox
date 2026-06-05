<?php

namespace Database\Seeders;

use App\Models\LotteryGame;
use App\Models\LotterySymbol;
use Illuminate\Database\Seeder;

class LotterySeeder extends Seeder
{
    public function run()
    {
        // Create symbols
        $symbols = [
            ['name' => 'divine_sword', 'display_name' => '⚔️ Divine Sword', 'icon' => 'fa-sword', 'weight' => 8, 'is_wild' => false, 'is_scatter' => false],
            ['name' => 'divine_bell', 'display_name' => '🔔 Divine Bell', 'icon' => 'fa-bell', 'weight' => 7, 'is_wild' => false, 'is_scatter' => false],
            ['name' => 'golden_flower', 'display_name' => '🌸 Divine Golden Flower', 'icon' => 'fa-fan', 'weight' => 3, 'is_wild' => false, 'is_scatter' => true],
            ['name' => 'frequency_8888', 'display_name' => '8888 Hz', 'icon' => 'fa-waveform', 'weight' => 2, 'is_wild' => false, 'is_scatter' => false],
            ['name' => 'frequency_7777', 'display_name' => '7777 Hz', 'icon' => 'fa-chart-line', 'weight' => 2, 'is_wild' => false, 'is_scatter' => false],
            ['name' => 'taurus', 'display_name' => '♉ Taurus', 'icon' => 'fa-bull', 'weight' => 5, 'is_wild' => false, 'is_scatter' => false],
            ['name' => 'tree_of_life', 'display_name' => '🌳 Tree of Life', 'icon' => 'fa-tree', 'weight' => 4, 'is_wild' => false, 'is_scatter' => false],
            ['name' => 'divine_star', 'display_name' => '⭐ Divine Star', 'icon' => 'fa-star', 'weight' => 1, 'is_wild' => false, 'is_scatter' => false],
        ];
        foreach ($symbols as $sym) {
            LotterySymbol::updateOrCreate(['name' => $sym['name']], $sym);
        }

        // Create game
        $game = LotteryGame::updateOrCreate(
            ['name' => 'Cosmic Slot'],
            [
                'description' => '3‑reel slot with divine frequencies and wild seals.',
                'ticket_price' => 10,
                'min_bet' => 10,
                'max_bet' => 1000,
                'reel_config' => json_encode(['reels' => 3, 'rows' => 1]),
                'is_active' => true,
                'free_spins_award' => 0,
                'jackpot_contribution_rate' => 5,
                'progressive_jackpot' => 1000,
            ]
        );

        // Payouts
        $payouts = [
            'divine_sword' => 10,
            'divine_bell' => 15,
            'golden_flower' => 30,
            'frequency_8888' => 50,
            'frequency_7777' => 50,
            'taurus' => 20,
            'tree_of_life' => 25,
            'divine_star' => 100,
        ];
        foreach ($payouts as $name => $mult) {
            $symbol = LotterySymbol::where('name', $name)->first();
            if ($symbol) {
                $game->symbols()->syncWithoutDetaching([$symbol->id => ['count' => 3, 'payout_multiplier' => $mult]]);
            }
        }
        // Wild combos
        $wild = LotterySymbol::where('name', 'divine_star')->first();
        if ($wild) {
            $game->symbols()->syncWithoutDetaching([$wild->id => ['count' => 2, 'payout_multiplier' => 2]]);
            $game->symbols()->syncWithoutDetaching([$wild->id => ['count' => 3, 'payout_multiplier' => 20]]);
        }
    }
}
