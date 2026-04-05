<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\InvestmentPlan;
use App\Services\Investment\InvestmentManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvestmentManagerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_an_investment_and_debits_wallet()
    {
        $user = User::factory()->create();
        $user->wallet->credit(10000, 'Test');

        $plan = InvestmentPlan::factory()->create([
            'min_amount' => 1000,
            'max_amount' => 5000,
            'daily_interest_rate' => 1.5,
            'duration_days' => 30,
        ]);

        $manager = app(InvestmentManager::class);
        $investment = $manager->create($user, $plan, 2000);

        $this->assertDatabaseHas('investments', [
            'id' => $investment->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => 2000,
        ]);

        $this->assertEquals(8000, $user->wallet->fresh()->balance);
    }
}
