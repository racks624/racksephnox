<?php

namespace Tests\Feature\Admin;

use App\Models\User;

class UserManagementTest extends AdminTestCase
{
    public function test_admin_can_view_users_list()
    {
        User::factory()->count(3)->create();
        
        $response = $this->get('/admin/users');
        $response->assertStatus(200);
    }

    public function test_admin_can_view_single_user()
    {
        $user = User::factory()->create();
        
        $response = $this->get("/admin/users/{$user->id}");
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($user->email);
    }

    public function test_admin_can_edit_user()
    {
        $user = User::factory()->create();
        
        $response = $this->put("/admin/users/{$user->id}", [
            'name' => 'Updated Name',
            'email' => $user->email,
        ]);
        
        $response->assertRedirect();
        $user->refresh();
        $this->assertEquals('Updated Name', $user->name);
    }

    public function test_admin_can_toggle_admin_status()
    {
        $user = User::factory()->create(['is_admin' => false]);
        
        $response = $this->post("/admin/users/{$user->id}/toggle-admin");
        
        $response->assertRedirect();
        $user->refresh();
        $this->assertTrue($user->is_admin);
    }

    public function test_admin_can_delete_user()
    {
        $user = User::factory()->create();
        
        $response = $this->delete("/admin/users/{$user->id}");
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
