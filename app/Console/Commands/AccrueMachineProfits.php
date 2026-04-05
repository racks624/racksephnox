<?php

namespace App\Console\Commands;

use App\Models\MachineInvestment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AccrueMachineProfits extends Command
{
    protected $signature = 'machines:accrue';
    protected $description = 'Accrue daily profits for active machine investments';

    public function handle()
    {
        $investments = MachineInvestment::where('status', 'active')->get();
        $count = 0;

        foreach ($investments as $inv) {
            DB::transaction(function () use ($inv, &$count) {
                // Credit daily profit to user's wallet
                $inv->user->wallet->credit($inv->daily_profit, 'Daily profit from machine ' . $inv->machine->name . ' VIP ' . $inv->vip_level, 'interest');

                // If end date passed, mark as completed
                if (now()->gte($inv->end_date)) {
                    $inv->update(['status' => 'completed']);
                }

                $count++;
            });
        }

        $this->info("Accrued profits for {$count} machine investments.");
    }
}
