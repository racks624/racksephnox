<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_can_be_rendered()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/profile');
        $response->assertStatus(200);
    }

    public function test_profile_information_can_be_updated()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->patch('/profile', [
            'name' => 'Test User Updated',
            'email' => 'test@example.com',
        ]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');
        $user->refresh();
        $this->assertSame('Test User Updated', $user->name);
    }
}
