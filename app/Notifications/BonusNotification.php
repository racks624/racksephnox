<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class BonusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $amount;
    public $type;

    public function __construct($amount, $type)
    {
        $this->amount = $amount;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        $messages = [
            'welcome' => '🎉 Welcome! KES ' . number_format($this->amount, 2) . ' credited as signup bonus.',
            'first_deposit' => '🎁 First deposit bonus: KES ' . number_format($this->amount, 2) . ' added.',
            'deposit_bonus' => '💰 Consecutive deposit bonus: KES ' . number_format($this->amount, 2) . ' added.',
            'trading_bonus' => '📈 Trading streak bonus: KES ' . number_format($this->amount, 2) . ' added.',
        ];
        return [
            'type' => 'bonus',
            'amount' => $this->amount,
            'bonus_type' => $this->type,
            'message' => $messages[$this->type] ?? 'Bonus credited: KES ' . number_format($this->amount, 2),
            'icon' => '🎁',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage($this->toArray($notifiable));
    }
}
