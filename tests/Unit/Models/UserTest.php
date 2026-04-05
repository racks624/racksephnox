<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_a_wallet_after_creation()
    {
        $user = User::factory()->create();
        $this->assertNotNull($user->wallet);
        $this->assertEquals(0, $user->wallet->balance);
    }
}
