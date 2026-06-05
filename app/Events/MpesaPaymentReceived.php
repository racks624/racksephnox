<?php

namespace App\Events;

use App\Models\MpesaTransaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MpesaPaymentReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transaction;

    public function __construct(MpesaTransaction $transaction)
    {
        $this->transaction = $transaction;
    }
}
