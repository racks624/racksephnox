<?php

namespace App\Jobs;

use App\Models\LotterySpin;
use App\Services\LotteryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessLotterySpin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $spinId;

    public function __construct($spinId)
    {
        $this->spinId = $spinId;
    }

    public function handle()
    {
        $spin = LotterySpin::find($this->spinId);
        if (!$spin) return;
        // Additional processing (e.g., update leaderboard, send notifications)
        // This is a placeholder for heavy tasks
    }
}
