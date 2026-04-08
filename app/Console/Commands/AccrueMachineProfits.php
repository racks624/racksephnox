<?php

namespace App\Console\Commands;

use App\Models\MachineInvestment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AccrueMachineProfits extends Command
{
    protected $signature = 'machines:accrue-profits';
    protected $description = 'Accrue daily profits for all active machine investments';

    public function handle()
    {
        $this->info('Starting daily profit accrual...');
        
        $investments = MachineInvestment::where('status', 'active')
            ->whereDate('end_date', '>=', now())
            ->get();
        
        $totalAccrued = 0;
        $count = 0;
        
        foreach ($investments as $investment) {
            $accrued = $investment->accrueDailyProfit();
            if ($accrued) {
                $totalAccrued += $accrued;
                $count++;
                $this->line("Accrued KES " . number_format($accrued, 2) . " for investment #{$investment->id}");
            }
        }
        
        $this->info("Completed! Accrued KES " . number_format($totalAccrued, 2) . " for {$count} investments.");
        
        Log::info("Machine profits accrued", [
            'count' => $count,
            'total' => $totalAccrued
        ]);
        
        return Command::SUCCESS;
    }
}
