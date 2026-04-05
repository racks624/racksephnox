<?php

namespace App\Jobs;

use App\Models\MpesaTransaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMpesaPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transaction;

    public function __construct(MpesaTransaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function handle()
    {
        // Simulate processing – in reality you might call a callback or API
        if ($this->transaction->status === MpesaTransaction::STATUS_COMPLETED) {
            $this->transaction->user->wallet->credit(
                $this->transaction->amount,
                'M-Pesa deposit: ' . $this->transaction->mpesa_receipt_number
            );
        }
    }
}
