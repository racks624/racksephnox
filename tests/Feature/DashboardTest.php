<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_see_dashboard()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Welcome back');
    }

    /** @test */
    public function guest_is_redirected_from_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }
}
