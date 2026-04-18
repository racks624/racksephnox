<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class JackpotWinNotification extends Notification
{
    use Queueable;

    protected $amount;
    protected $type; // 'mini' or 'super'

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
        $subject = $this->type === 'super' ? '🌟 SUPER JACKPOT WIN!' : '🌸 MINI JACKPOT WIN!';
        return (new MailMessage)
            ->subject($subject)
            ->line("Congratulations! You won KES " . number_format($this->amount, 2))
            ->line("Your cosmic luck has manifested divine wealth.")
            ->action('Play Again', url('/lottery'))
            ->line('Thank you for being part of Racksephnox!');
    }

    public function toArray($notifiable)
    {
        return [
            'amount' => $this->amount,
            'type' => $this->type,
            'message' => $this->type === 'super' ? '🌟 SUPER JACKPOT WIN!' : '🌸 MINI JACKPOT WIN!',
        ];
    }
}
