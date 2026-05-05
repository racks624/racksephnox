<?php

namespace Database\Seeders;

use App\Models\LotteryGame;
use Illuminate\Database\Seeder;

class LotteryGameSeeder extends Seeder
{
    public function run()
    {
        LotteryGame::updateOrCreate(
            ['name' => 'Divine Cosmic Slots'],
            [
                'description' => '8888 Hz frequency slot machine with progressive jackpot',
                'min_bet' => 10,
                'max_bet' => 1000,
                'ticket_price' => 10,
                'is_active' => true,
                'progressive_jackpot' => 1000,
                'jackpot_contribution_rate' => 5,
                'base_rtp' => 95,
                'vip_rtp' => 97,
                'promo_rtp' => 99,
                'free_spins_award' => 0,
                'volatility' => 'medium',
                'enable_free_spins' => true,
                'enable_bonus_buy' => true,
                'bonus_buy_price' => 100,
            ]
        );
    }
}
