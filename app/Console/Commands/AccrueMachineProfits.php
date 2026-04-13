<?php

namespace App\Console\Commands;

use App\Models\MachineInvestment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AccrueMachineProfits extends Command
{
    protected $signature = 'machines:accrue-profits';
    protected $description = 'Accrue daily profits for active machine investments';

    public function handle()
    {
        $this->info('Starting daily profit accrual...');
        
        // Get all active investments that haven't matured yet
        $investments = MachineInvestment::where('status', 'active')
            ->where('end_date', '>=', now())
            ->get();
        
        $this->info("Found {$investments->count()} active investments");
        
        $totalAccrued = 0;
        $count = 0;
        
        foreach ($investments as $investment) {
            $this->line("Processing investment #{$investment->id}");
            
            // Check if already accrued today
            $lastProfitDate = $investment->last_profit_date ? \Carbon\Carbon::parse($investment->last_profit_date) : null;
            
            if ($lastProfitDate && $lastProfitDate->isToday()) {
                $this->line("Already accrued today for investment #{$investment->id}");
                continue;
            }
            
            $wallet = $investment->user->wallet;
            if (!$wallet) {
                $this->error("User {$investment->user_id} has no wallet");
                continue;
            }
            
            // Accrue profit
            $investment->profit_credited += $investment->daily_profit;
            $investment->last_profit_date = now();
            $investment->save();
            
            // Credit wallet
            $wallet->increment('balance', $investment->daily_profit);
            
            // Record transaction
            $investment->user->transactions()->create([
                'user_id'       => $investment->user_id,
                'wallet_id'     => $wallet->id,
                'type'          => 'machine_interest',
                'amount'        => $investment->daily_profit,
                'status'        => 'completed',
                'description'   => "Daily profit from {$investment->machine->name} (VIP {$investment->vip_level})",
                'balance_after' => $wallet->balance,
            ]);
            
            $totalAccrued += $investment->daily_profit;
            $count++;
            $this->line("Accrued KES " . number_format($investment->daily_profit, 2) . " for investment #{$investment->id}");
            $this->line("New wallet balance: KES " . number_format($wallet->balance, 2));
        }
        
        $this->info("Completed! Accrued KES " . number_format($totalAccrued, 2) . " for {$count} investments.");
        Log::info("Machine profits accrued", ['count' => $count, 'total' => $totalAccrued]);
        
        return Command::SUCCESS;
    }
}
