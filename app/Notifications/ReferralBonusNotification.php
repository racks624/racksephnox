<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReferralBonusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $amount;
    public $referralName;

    public function __construct($amount, $referralName)
    {
        $this->amount = $amount;
        $this->referralName = $referralName;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'referral_bonus',
            'amount' => $this->amount,
            'referral' => $this->referralName,
            'message' => "🎁 KES " . number_format($this->amount, 2) . " bonus from referral {$this->referralName}",
            'icon' => '🎁',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage($this->toArray($notifiable));
    }
}
