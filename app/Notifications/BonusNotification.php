<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BonusNotification extends Notification
{
    use Queueable;

    protected $amount;
    protected $type;

    public function __construct($amount, $type)
    {
        $this->amount = $amount;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🎉 Trading Bonus Awarded!')
            ->line("You've earned a {$this->type} bonus of KES " . number_format($this->amount, 2))
            ->line('Keep trading to unlock more rewards.')
            ->action('Start Trading', url('/trading'));
    }

    public function toArray($notifiable)
    {
        return [
            'amount' => $this->amount,
            'type' => $this->type,
            'message' => "You received a {$this->type} bonus of KES " . number_format($this->amount, 2),
        ];
    }
}
