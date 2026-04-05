<?php

namespace App\Notifications;

use App\Models\MpesaTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DepositConfirmed extends Notification
{
    use Queueable;

    protected $transaction;

    public function __construct(MpesaTransaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'KES ' . number_format($this->transaction->amount, 2) . ' deposited to your wallet.',
            'transaction_id' => $this->transaction->id,
            'receipt' => $this->transaction->mpesa_receipt_number,
            'time' => now()->toDateTimeString(),
        ];
    }
}
