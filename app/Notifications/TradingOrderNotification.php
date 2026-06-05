<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class TradingOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $side;
    public $amountBtc;
    public $price;
    public $status;

    public function __construct($side, $amountBtc, $price, $status)
    {
        $this->side = $side;
        $this->amountBtc = $amountBtc;
        $this->price = $price;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        $action = $this->side === 'buy' ? 'Bought' : 'Sold';
        return [
            'type' => 'trade',
            'side' => $this->side,
            'amount_btc' => $this->amountBtc,
            'price' => $this->price,
            'status' => $this->status,
            'message' => "📊 {$action} " . number_format($this->amountBtc, 6) . " BTC @ KES " . number_format($this->price, 2),
            'icon' => '📊',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage($this->toArray($notifiable));
    }
}
