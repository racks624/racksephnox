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
}
