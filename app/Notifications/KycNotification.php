<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class KycNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $status;
    public $reason;

    public function __construct($status, $reason = null)
    {
        $this->status = $status;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        $subject = $this->status === 'approved' ? '✅ KYC Approved' : '❌ KYC Rejected';
        $line = $this->status === 'approved' 
            ? 'Your KYC verification has been approved. You now have full access to all features.'
            : 'Your KYC verification was rejected. Reason: ' . $this->reason;
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject($subject . ' – Racksephnox')
            ->greeting('Hello ' . $notifiable->name)
            ->line($line)
            ->action('View KYC Status', url('/kyc'));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'kyc',
            'status' => $this->status,
            'reason' => $this->reason,
            'message' => $this->status === 'approved' 
                ? '✅ Your KYC has been approved.' 
                : '❌ Your KYC was rejected: ' . $this->reason,
            'icon' => $this->status === 'approved' ? '✅' : '❌',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage($this->toArray($notifiable));
    }
}
