<?php

namespace App\Notifications;

use App\Models\Investment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InvestmentMatured extends Notification
{
    use Queueable;

    protected $investment;

    public function __construct(Investment $investment)
    {
        $this->investment = $investment;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Your investment of KES ' . number_format($this->investment->amount, 2) . ' in ' . $this->investment->plan->name . ' has matured.',
            'investment_id' => $this->investment->id,
            'time' => now()->toDateTimeString(),
        ];
    }
}
