<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\WithdrawalRequest;
use App\Models\Wallet;

class WithdrawalProcessingTest extends AdminTestCase
{
    public function test_admin_can_view_withdrawals_list()
    {
        WithdrawalRequest::create([
            'user_id' => User::factory()->create()->id,
            'amount' => 500,
            'status' => 'pending',
        ]);
        
        $response = $this->get('/admin/withdrawals');
        $response->assertStatus(200);
    }

    public function test_admin_can_process_withdrawal()
    {
        $user = User::factory()->create();
        $wallet = $user->wallet()->create(['balance' => 1000]);
        
        $withdrawal = WithdrawalRequest::create([
            'user_id' => $user->id,
            'amount' => 500,
            'status' => 'pending',
        ]);
        
        $response = $this->post("/admin/withdrawals/{$withdrawal->id}/process");
        
        $response->assertRedirect();
        $withdrawal->refresh();
        $wallet->refresh();
        $this->assertEquals('processed', $withdrawal->status);
        $this->assertEquals(500, $wallet->balance);
    }

    public function test_admin_can_complete_withdrawal()
    {
        $withdrawal = WithdrawalRequest::create([
            'user_id' => User::factory()->create()->id,
            'amount' => 500,
            'status' => 'processed',
        ]);
        
        $response = $this->post("/admin/withdrawals/{$withdrawal->id}/complete");
        
        $response->assertRedirect();
        $withdrawal->refresh();
        $this->assertEquals('completed', $withdrawal->status);
    }

    public function test_admin_can_reject_withdrawal()
    {
        $user = User::factory()->create();
        $wallet = $user->wallet()->create(['balance' => 1000]);
        
        $withdrawal = WithdrawalRequest::create([
            'user_id' => $user->id,
            'amount' => 500,
            'status' => 'pending',
        ]);
        
        $response = $this->post("/admin/withdrawals/{$withdrawal->id}/reject");
        
        $response->assertRedirect();
        $withdrawal->refresh();
        $wallet->refresh();
        $this->assertEquals('rejected', $withdrawal->status);
        $this->assertEquals(1000, $wallet->balance);
    }
}
