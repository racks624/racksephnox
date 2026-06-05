<?php
namespace App\Console\Commands;
use App\Models\LotterySpin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
class UpdateLeaderboardCache extends Command
{
    protected $signature = 'lottery:cache-leaderboard';
    public function handle()
    {
        $leaderboard = LotterySpin::where('created_at', '>=', now()->startOfWeek())
            ->selectRaw('user_id, SUM(win_amount) as total_win')
            ->groupBy('user_id')
            ->orderBy('total_win', 'desc')
            ->with('user')
            ->take(50)
            ->get();
        Cache::put('lottery_weekly_leaderboard', $leaderboard, 3600);
        $this->info('Leaderboard cached.');
    }
}
