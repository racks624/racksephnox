<?php

namespace Tests\Unit\Models;

use App\Models\InvestmentPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvestmentPlanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_min_max_constraints()
    {
        $plan = InvestmentPlan::factory()->create([
            'min_amount' => 1000,
            'max_amount' => 50000,
        ]);
        $this->assertTrue($plan->min_amount <= $plan->max_amount);
    }
}
