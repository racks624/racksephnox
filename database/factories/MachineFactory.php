<?php

namespace Database\Factories;

use App\Models\Machine;
use Illuminate\Database\Eloquent\Factories\Factory;

class MachineFactory extends Factory
{
    protected $model = Machine::class;

    public function definition()
    {
        $phi = 1.61803398875;
        $vip1 = $this->faker->numberBetween(1000, 50000);
        
        return [
            'code' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{3}'),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'vip1_start_amount' => $vip1,
            'vip2_start_amount' => round($vip1 * $phi, 2),
            'vip3_start_amount' => round($vip1 * pow($phi, 2), 2),
            'duration_days' => 14,
            'growth_rate' => $this->faker->numberBetween(20, 35),
            'risk_profile' => $this->faker->randomElement(['Low', 'Medium', 'High']),
            'icon' => 'fa-microchip',
            'color' => 'from-gold-400 to-amber-400',
            'is_active' => true,
            'early_withdrawal_penalty' => 20,
            'referral_bonus_rate' => 5,
        ];
    }
}
