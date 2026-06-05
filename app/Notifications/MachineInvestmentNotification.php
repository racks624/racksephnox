<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class MachineInvestmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $machineName;
    public $vipLevel;
    public $amount;

    public function __construct($machineName, $vipLevel, $amount)
    {
        $this->machineName = $machineName;
        $this->vipLevel = $vipLevel;
        $this->amount = $amount;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'machine_investment',
            'machine' => $this->machineName,
            'vip_level' => $this->vipLevel,
            'amount' => $this->amount,
            'message' => "🤖 Invested KES " . number_format($this->amount, 2) . " in {$this->machineName} (VIP {$this->vipLevel})",
            'icon' => '🤖',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new \Illuminate\Notifications\Messages\BroadcastMessage($this->toArray($notifiable));
    }
}
