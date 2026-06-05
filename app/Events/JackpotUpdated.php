<?php
namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class JackpotUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $jackpot;
    public function __construct($jackpot) { $this->jackpot = $jackpot; }
    public function broadcastOn() { return new Channel('lottery'); }
    public function broadcastAs() { return 'jackpot.updated'; }
}
