<?php

namespace App\Events;

use App\Models\Wallet;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WalletBalanceUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $wallet;

    public function __construct(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }

    public function broadcastOn()
    {
        return new Channel('user.' . $this->wallet->user_id);
    }

    public function broadcastWith()
    {
        return [
            'balance' => $this->wallet->balance,
            'locked_balance' => $this->wallet->locked_balance,
            'updated_at' => $this->wallet->updated_at->toIso8601String(),
        ];
    }
}
