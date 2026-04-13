<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\DepositRequest;
use App\Models\Wallet;

class DepositVerificationTest extends AdminTestCase
{
    public function test_admin_can_view_deposits_list()
    {
        DepositRequest::create([
            'user_id' => User::factory()->create()->id,
            'amount' => 1000,
            'transaction_code' => 'DEP123',
            'status' => 'pending',
        ]);
        
        $response = $this->get('/admin/deposits');
        $response->assertStatus(200);
    }

    public function test_admin_can_verify_deposit()
    {
        $user = User::factory()->create();
        $wallet = $user->wallet()->create(['balance' => 0]);
        
        $deposit = DepositRequest::create([
            'user_id' => $user->id,
            'amount' => 1000,
            'transaction_code' => 'DEP123',
            'status' => 'pending',
        ]);
        
        $response = $this->post("/admin/deposits/{$deposit->id}/verify");
        
        $response->assertRedirect();
        $deposit->refresh();
        $wallet->refresh();
        $this->assertEquals('verified', $deposit->status);
        $this->assertEquals(1000, $wallet->balance);
    }

    public function test_admin_can_reject_deposit()
    {
        $deposit = DepositRequest::create([
            'user_id' => User::factory()->create()->id,
            'amount' => 1000,
            'transaction_code' => 'DEP123',
            'status' => 'pending',
        ]);
        
        $response = $this->post("/admin/deposits/{$deposit->id}/reject");
        
        $response->assertRedirect();
        $deposit->refresh();
        $this->assertEquals('rejected', $deposit->status);
    }
}
