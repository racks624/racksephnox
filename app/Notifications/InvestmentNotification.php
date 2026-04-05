<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvestmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $planName;
    public $amount;

    public function __construct($planName, $amount)
    {
        $this->planName = $planName;
        $this->amount = $amount;
    }

    public function via($notifiable)
    {
        $prefs = $notifiable->notification_preferences ?? [];
        $channels = [];
        if (($prefs['email_investment'] ?? true)) $channels[] = 'mail';
        if (($prefs['database_investment'] ?? true)) $channels[] = 'database';
        if (($prefs['broadcast_investment'] ?? true)) $channels[] = 'broadcast';
        return $channels;
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('🌱 Investment Created – Racksephnox')
            ->greeting('Hello ' . $notifiable->name)
            ->line('You have invested **KES ' . number_format($this->amount, 2) . '** in the **' . $this->planName . '** plan.')
            ->action('View Investments', url('/investments'))
            ->line('Daily profits will be credited to your wallet.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'investment',
            'plan' => $this->planName,
            'amount' => $this->amount,
            'message' => '🌱 KES ' . number_format($this->amount, 2) . ' invested in ' . $this->planName,
            'icon' => '🌱',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage($this->toArray($notifiable));
    }
}
