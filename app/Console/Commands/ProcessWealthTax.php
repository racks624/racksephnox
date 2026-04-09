<?php

namespace App\Console\Commands;

use App\Models\MachineInvestment;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessWealthTax extends Command
{
    protected $signature = 'racksephnox:process-frequency-tax {frequency=daily}';
    protected $description = 'Process 8888 Hz Wealth Tax for Divine Treasury';

    const DAILY_TAX_RATE = 0.0088;
    const WEEKLY_TAX_RATE = 0.08888;
    const MONTHLY_TAX_RATE = 0.88888888;
    const YEARLY_TAX_RATE = 0.8888888888888;

    public function handle()
    {
        $frequency = $this->argument('frequency');
        $taxRate = match ($frequency) {
            'daily'   => self::DAILY_TAX_RATE,
            'weekly'  => self::WEEKLY_TAX_RATE,
            'monthly' => self::MONTHLY_TAX_RATE,
            'yearly'  => self::YEARLY_TAX_RATE,
            default   => self::DAILY_TAX_RATE,
        };

        $this->info("Processing {$frequency} Wealth Tax at {$taxRate * 100}%...");

        $investments = MachineInvestment::where('status', 'active')->get();
        $totalTaxCollected = 0;
        $count = 0;

        foreach ($investments as $investment) {
            $profit = match ($frequency) {
                'daily'   => $investment->daily_profit,
                'weekly'  => $investment->daily_profit * 7,
                'monthly' => $investment->daily_profit * 30,
                'yearly'  => $investment->daily_profit * 365,
                default   => $investment->daily_profit,
            };

            $taxAmount = round($profit * $taxRate, 2);
            if ($taxAmount <= 0) continue;

            // Record tax to Divine Treasury (special user ID 0 or a treasury account)
            DB::table('wealth_tax_logs')->insert([
                'investment_id' => $investment->id,
                'user_id' => $investment->user_id,
                'machine_id' => $investment->machine_id,
                'frequency' => $frequency,
                'profit_amount' => $profit,
                'tax_amount' => $taxAmount,
                'tax_rate' => $taxRate,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $totalTaxCollected += $taxAmount;
            $count++;

            $this->line("Tax collected: KES {$taxAmount} from investment #{$investment->id}");
        }

        $this->info("✅ {$frequency} Wealth Tax complete!");
        $this->info("Total Tax Collected: KES " . number_format($totalTaxCollected, 2));
        $this->info("Affected Investments: {$count}");

        Log::info("Wealth Tax Processed", [
            'frequency' => $frequency,
            'tax_rate' => $taxRate,
            'total_tax' => $totalTaxCollected,
            'count' => $count,
        ]);

        return Command::SUCCESS;
    }
}
