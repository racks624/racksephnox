<?php
namespace App\Console\Commands;
use App\Models\LotterySpin;
use Illuminate\Console\Command;
class CleanupLotterySpins extends Command
{
    protected $signature = 'lottery:cleanup-spins';
    protected $description = 'Delete old lottery spins';
    public function handle() {
        $deleted = LotterySpin::where('created_at', '<', now()->subDays(30))->delete();
        $this->info("Deleted {$deleted} old spins.");
    }
}
