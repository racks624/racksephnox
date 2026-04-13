<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DepositConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transaction;

    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    public function via($notifiable)
    {
        $prefs = $notifiable->notification_preferences ?? [];
        $channels = [];
        if ($prefs['email_deposit'] ?? false) $channels[] = 'mail';
        if ($prefs['database_deposit'] ?? false) $channels[] = 'database';
        if ($prefs['broadcast_deposit'] ?? false) $channels[] = 'broadcast';
        return $channels;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Deposit Confirmed')
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your deposit of KES " . number_format($this->transaction->amount, 2) . " has been confirmed.")
            ->action('View Wallet', url('/wallet'))
            ->line('Thank you for investing with Racksephnox.');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Deposit of KES " . number_format($this->transaction->amount, 2) . " completed.",
            'icon' => '💰',
            'category' => 'deposit',
        ];
    }
}
