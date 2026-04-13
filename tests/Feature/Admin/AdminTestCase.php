<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class AdminTestCase extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'phone' => '+254712345678',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'is_verified' => true,
            'kyc_status' => 'verified',
            'email_verified_at' => now(),
        ]);
        
        // Create wallet for admin
        $this->admin->wallet()->create(['balance' => 0]);
        
        $this->actingAs($this->admin);
    }
}
