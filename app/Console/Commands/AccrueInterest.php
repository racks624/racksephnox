<?php

namespace App\Console\Commands;

use App\Models\Investment;
use App\Services\Investment\InvestmentService;
use Illuminate\Console\Command;

class AccrueInterest extends Command
{
    protected $signature = 'investments:accrue';
    protected $description = 'Accrue daily interest for all active investments';

    protected $investmentService;

    public function __construct(InvestmentService $investmentService)
    {
        parent::__construct();
        $this->investmentService = $investmentService;
    }

    public function handle()
    {
        $investments = Investment::where('status', Investment::STATUS_ACTIVE)->get();
        $count = 0;
        foreach ($investments as $investment) {
            try {
                $this->investmentService->accrueProfit($investment);
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to accrue for investment {$investment->id}: " . $e->getMessage());
            }
        }
        $this->info("Accrued profit for {$count} investments.");
    }
}
