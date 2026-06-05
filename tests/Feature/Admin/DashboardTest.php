<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\DepositRequest;
use App\Models\WithdrawalRequest;

class DashboardTest extends AdminTestCase
{
    public function test_admin_can_access_dashboard()
    {
        $response = $this->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
    }

    public function test_admin_sees_correct_statistics()
    {
        // Create regular users
        User::factory()->count(5)->create();
        
        // Create pending deposit
        DepositRequest::create([
            'user_id' => User::factory()->create()->id,
            'amount' => 1000,
            'transaction_code' => 'DEP123',
            'status' => 'pending',
        ]);
        
        // Create pending withdrawal
        WithdrawalRequest::create([
            'user_id' => User::factory()->create()->id,
            'amount' => 500,
            'status' => 'pending',
        ]);
        
        $response = $this->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Total Users');
        $response->assertSee('Pending Deposits');
        $response->assertSee('Pending Withdrawals');
    }
}
