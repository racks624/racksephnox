<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InvestmentMatured extends Notification implements ShouldQueue
{
    use Queueable;

    protected $investment;

    public function __construct($investment)
    {
        $this->investment = $investment;
    }

    public function via($notifiable)
    {
        $prefs = $notifiable->notification_preferences ?? [];
        $channels = [];
        if (isset($prefs['email_investment']) && $prefs['email_investment']) $channels[] = 'mail';
        if (isset($prefs['broadcast_investment']) && $prefs['broadcast_investment']) $channels[] = 'broadcast';
        return $channels;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Investment Has Matured')
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your investment in {$this->investment->machine->name} (VIP {$this->investment->vip_level}) has matured.")
            ->line("Total return: KES " . number_format($this->investment->total_return, 2))
            ->action('View Dashboard', url('/dashboard'))
            ->line('Thank you for trusting Racksephnox!');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Investment in {$this->investment->machine->name} VIP {$this->investment->vip_level} has matured. Return: KES " . number_format($this->investment->total_return, 2),
            'icon' => '💰',
            'category' => 'investment',
        ];
    }
}
