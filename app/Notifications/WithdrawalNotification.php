<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class WithdrawalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $amount;
    public $status;

    public function __construct($amount, $status = 'pending')
    {
        $this->amount = $amount;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        $prefs = $notifiable->notification_preferences ?? [];
        $channels = [];
        if (($prefs['email_withdrawal'] ?? true)) $channels[] = 'mail';
        if (($prefs['database_withdrawal'] ?? true)) $channels[] = 'database';
        if (($prefs['broadcast_withdrawal'] ?? true)) $channels[] = 'broadcast';
        return $channels;
    }

    public function toMail($notifiable)
    {
        $subject = $this->status === 'completed' ? '✅ Withdrawal Processed' : '⏳ Withdrawal Request Received';
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject($subject . ' – Racksephnox')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your withdrawal request of **KES ' . number_format($this->amount, 2) . '** is **' . $this->status . '**.')
            ->action('View Wallet', url('/wallet'))
            ->line('Thank you for banking with Racksephnox.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'withdrawal',
            'amount' => $this->amount,
            'status' => $this->status,
            'message' => $this->status === 'completed' 
                ? '✅ KES ' . number_format($this->amount, 2) . ' withdrawal completed.' 
                : '⏳ KES ' . number_format($this->amount, 2) . ' withdrawal requested.',
            'icon' => $this->status === 'completed' ? '✅' : '⏳',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage($this->toArray($notifiable));
    }
}
