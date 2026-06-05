<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class DepositNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $amount;
    public $method;

    public function __construct($amount, $method = 'M-Pesa')
    {
        $this->amount = $amount;
        $this->method = $method;
    }

    public function via($notifiable)
    {
        $prefs = $notifiable->notification_preferences ?? [];
        $channels = [];
        if (($prefs['email_deposit'] ?? true)) $channels[] = 'mail';
        if (($prefs['database_deposit'] ?? true)) $channels[] = 'database';
        if (($prefs['broadcast_deposit'] ?? true)) $channels[] = 'broadcast';
        return $channels;
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('💰 Deposit Confirmed – Racksephnox')
            ->greeting('Hello ' . $notifiable->name)
            ->line('A deposit of **KES ' . number_format($this->amount, 2) . '** has been confirmed via ' . $this->method . '.')
            ->action('View Wallet', url('/wallet'))
            ->line('Thank you for trusting Racksephnox.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'deposit',
            'amount' => $this->amount,
            'method' => $this->method,
            'message' => '✨ KES ' . number_format($this->amount, 2) . ' deposited successfully.',
            'icon' => '💰',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage($this->toArray($notifiable));
    }
}
