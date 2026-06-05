<?php
namespace Database\Factories;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class UserFactory extends Factory
{
    protected $model = User::class;
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => '254' . $this->faker->numerify('#########'),
            'password' => Hash::make('password'),
            'referral_code' => Str::random(8),
            'is_verified' => true,
            'email_verified_at' => now(),
        ];
    }
}
