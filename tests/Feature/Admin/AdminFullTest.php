<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Wallet;
use App\Models\KycDocument;
use App\Models\DepositRequest;
use App\Models\WithdrawalRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminFullTest extends TestCase
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
            'referral_code' => 'ADMIN123',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'is_verified' => true,
            'kyc_status' => 'verified',
            'email_verified_at' => now(),
        ]);
        
        Wallet::create(['user_id' => $this->admin->id, 'balance' => 0]);
        $this->actingAs($this->admin);
    }

    // Dashboard
    public function test_admin_can_access_dashboard()
    {
        $response = $this->get('/admin');
        $response->assertStatus(200);
    }

    // User management
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
    }

    public function test_admin_can_edit_user()
    {
        $user = User::factory()->create();
        $response = $this->put("/admin/users/{$user->id}", [
            'name' => 'Updated Name',
            'email' => $user->email,
        ]);
        $response->assertRedirect();
    }

    public function test_admin_can_delete_user()
    {
        $user = User::factory()->create();
        $response = $this->delete("/admin/users/{$user->id}");
        $response->assertRedirect();
    }

    // KYC
    public function test_admin_can_view_kyc_list()
    {
        KycDocument::create([
            'user_id' => User::factory()->create()->id,
            'document_type' => 'national_id',
            'document_path' => 'path/to/doc.jpg',
            'status' => 'pending',
        ]);
        $response = $this->get('/admin/kyc');
        $response->assertStatus(200);
    }

    public function test_admin_can_approve_kyc()
    {
        $user = User::factory()->create(['is_verified' => false, 'kyc_status' => 'pending']);
        $doc = KycDocument::create([
            'user_id' => $user->id,
            'document_type' => 'national_id',
            'document_path' => 'path/to/doc.jpg',
            'status' => 'pending',
        ]);
        $response = $this->post("/admin/kyc/{$doc->id}/approve");
        $response->assertRedirect();
    }

    public function test_admin_can_reject_kyc()
    {
        $doc = KycDocument::create([
            'user_id' => User::factory()->create()->id,
            'document_type' => 'national_id',
            'document_path' => 'path/to/doc.jpg',
            'status' => 'pending',
        ]);
        $response = $this->post("/admin/kyc/{$doc->id}/reject", ['reason' => 'Blurry']);
        $response->assertRedirect();
    }

    // Deposits
    public function test_admin_can_view_deposits_list()
    {
        DepositRequest::create([
            'user_id' => User::factory()->create()->id,
            'amount' => 1000,
            'phone_number' => '254712345678',
            'transaction_reference' => 'DEP123',
            'status' => 'pending',
        ]);
        $response = $this->get('/admin/deposits');
        $response->assertStatus(200);
    }

    public function test_admin_can_verify_deposit()
    {
        $user = User::factory()->create();
        Wallet::create(['user_id' => $user->id, 'balance' => 0]);
        $deposit = DepositRequest::create([
            'user_id' => $user->id,
            'amount' => 1000,
            'phone_number' => '254712345678',
            'transaction_reference' => 'DEP123',
            'status' => 'pending',
        ]);
        $response = $this->post("/admin/deposits/{$deposit->id}/verify");
        $response->assertRedirect();
    }

    public function test_admin_can_reject_deposit()
    {
        $deposit = DepositRequest::create([
            'user_id' => User::factory()->create()->id,
            'amount' => 1000,
            'phone_number' => '254712345678',
            'transaction_reference' => 'DEP123',
            'status' => 'pending',
        ]);
        $response = $this->post("/admin/deposits/{$deposit->id}/reject");
        $response->assertRedirect();
    }

    // Withdrawals
    public function test_admin_can_view_withdrawals_list()
    {
        WithdrawalRequest::create([
            'user_id' => User::factory()->create()->id,
            'amount' => 500,
            'fee' => 10,
            'net_amount' => 490,
            'phone' => '254712345678',
            'status' => 'pending',
        ]);
        $response = $this->get('/admin/withdrawals');
        $response->assertStatus(200);
    }

    public function test_admin_can_process_withdrawal()
    {
        $user = User::factory()->create();
        Wallet::create(['user_id' => $user->id, 'balance' => 1000]);
        $withdrawal = WithdrawalRequest::create([
            'user_id' => $user->id,
            'amount' => 500,
            'fee' => 10,
            'net_amount' => 490,
            'phone' => '254712345678',
            'status' => 'pending',
        ]);
        $response = $this->post("/admin/withdrawals/{$withdrawal->id}/process");
        $response->assertRedirect();
    }

    public function test_admin_can_complete_withdrawal()
    {
        $withdrawal = WithdrawalRequest::create([
            'user_id' => User::factory()->create()->id,
            'amount' => 500,
            'fee' => 10,
            'net_amount' => 490,
            'phone' => '254712345678',
            'status' => 'processed',
        ]);
        $response = $this->post("/admin/withdrawals/{$withdrawal->id}/complete");
        $response->assertRedirect();
    }

    public function test_admin_can_reject_withdrawal()
    {
        $withdrawal = WithdrawalRequest::create([
            'user_id' => User::factory()->create()->id,
            'amount' => 500,
            'fee' => 10,
            'net_amount' => 490,
            'phone' => '254712345678',
            'status' => 'pending',
        ]);
        $response = $this->post("/admin/withdrawals/{$withdrawal->id}/reject");
        $response->assertRedirect();
    }

    // Reports
    public function test_admin_can_view_reports_page()
    {
        $response = $this->get('/admin/reports');
        $response->assertStatus(200);
    }
}
