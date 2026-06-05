<?php
namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class BigWinEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $userName, $amount, $symbols;
    public function __construct($userName, $amount, $symbols)
    {
        $this->userName = $userName;
        $this->amount = $amount;
        $this->symbols = $symbols;
    }
    public function broadcastOn() { return new Channel('lottery'); }
    public function broadcastAs() { return 'bigwin'; }
}
