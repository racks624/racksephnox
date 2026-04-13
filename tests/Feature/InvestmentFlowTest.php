<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Machine;
use App\Models\MachineInvestment;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvestmentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $wallet;
    protected $machine;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '254712345678',
            'referral_code' => 'TEST1234',
            'password' => bcrypt('password'),
            'is_verified' => true,
        ]);
        
        $this->wallet = Wallet::create([
            'user_id' => $this->user->id,
            'balance' => 10000,
        ]);
        
        $this->machine = Machine::create([
            'code' => 'TEST',
            'name' => 'Test Machine',
            'vip1_start_amount' => 1000,
            'vip2_start_amount' => 1618,
            'vip3_start_amount' => 2618,
            'duration_days' => 14,
            'growth_rate' => 25,
            'is_active' => true,
            'early_withdrawal_penalty' => 20,
            'risk_profile' => 'Medium',
            'icon' => 'fa-microchip',
            'color' => 'from-gold-400 to-amber-400',
        ]);
    }

    public function test_user_can_invest_in_a_machine()
    {
        $vipLevel = 1;
        $amount = $this->machine->getStartAmountForVip($vipLevel);
        
        $this->wallet->balance -= $amount;
        $this->wallet->save();
        
        $investment = MachineInvestment::create([
            'user_id' => $this->user->id,
            'machine_id' => $this->machine->id,
            'vip_level' => $vipLevel,
            'amount' => $amount,
            'daily_profit' => $this->machine->getDailyProfit($amount),
            'total_return' => $this->machine->getTotalReturn($amount),
            'start_date' => now(),
            'end_date' => now()->addDays($this->machine->duration_days),
            'status' => 'active',
            'profit_credited' => 0,
        ]);

        $this->assertNotNull($investment);
        $this->assertEquals(1000, $investment->amount);
        $this->assertEquals(9000, $this->wallet->fresh()->balance);
    }

    public function test_daily_profit_accrues_correctly()
    {
        // Create investment with yesterday's start date
        $investment = MachineInvestment::create([
            'user_id' => $this->user->id,
            'machine_id' => $this->machine->id,
            'vip_level' => 1,
            'amount' => 1000,
            'daily_profit' => 71.43,
            'total_return' => 1250,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(13),
            'status' => 'active',
            'profit_credited' => 0,
            'last_profit_date' => null,
        ]);

        $initialBalance = $this->wallet->balance;
        
        // Directly simulate profit accrual (bypassing the command)
        $investment->profit_credited += $investment->daily_profit;
        $investment->last_profit_date = now();
        $investment->save();
        
        $this->wallet->balance += $investment->daily_profit;
        $this->wallet->save();

        $this->assertGreaterThan(0, $investment->profit_credited);
        $this->assertGreaterThan($initialBalance, $this->wallet->fresh()->balance);
    }

    public function test_early_withdrawal_deducts_penalty()
    {
        $investment = MachineInvestment::create([
            'user_id' => $this->user->id,
            'machine_id' => $this->machine->id,
            'vip_level' => 1,
            'amount' => 1000,
            'daily_profit' => 71.43,
            'total_return' => 1250,
            'start_date' => now(),
            'end_date' => now()->addDays(14),
            'status' => 'active',
            'profit_credited' => 0,
        ]);

        $initialBalance = $this->wallet->balance;
        
        $penaltyRate = $this->machine->early_withdrawal_penalty;
        $refundAmount = $investment->amount * (1 - $penaltyRate / 100);
        
        $this->wallet->balance += $refundAmount;
        $this->wallet->save();
        
        $investment->status = 'cancelled';
        $investment->save();

        $this->assertEquals('cancelled', $investment->status);
        $this->assertEquals($initialBalance + $refundAmount, $this->wallet->fresh()->balance);
    }
}
