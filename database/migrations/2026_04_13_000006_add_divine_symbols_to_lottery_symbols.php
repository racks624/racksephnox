<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\LotterySymbol;

return new class extends Migration
{
    public function up()
    {
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
    }

    public function down()
    {
        $names = ['divine_sword', 'divine_bell', 'golden_flower', 'frequency_8888', 'frequency_7777', 'taurus', 'tree_of_life', 'divine_star'];
        LotterySymbol::whereIn('name', $names)->delete();
    }
};
