<?php

namespace App\Console\Commands;

use App\Models\MpesaTransaction;
use Illuminate\Console\Command;

class CheckMpesaTransactions extends Command
{
    protected $signature = 'mpesa:check-pending';
    protected $description = 'Check pending M-Pesa transactions';

    public function handle()
    {
        // This would query M-Pesa API for status of pending transactions
        $pending = MpesaTransaction::where('status', MpesaTransaction::STATUS_PENDING)->get();

        foreach ($pending as $transaction) {
            // In production, call M-Pesa query API
            $this->info("Checking transaction {$transaction->id}");
        }

        $this->info("Checked {$pending->count()} pending transactions.");
    }
}
