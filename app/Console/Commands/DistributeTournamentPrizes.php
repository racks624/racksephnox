<?php
namespace App\Console\Commands;
use App\Models\LotteryTournament;
use Illuminate\Console\Command;
class DistributeTournamentPrizes extends Command
{
    protected $signature = 'lottery:distribute-prizes';
    protected $description = 'Distribute prizes to tournament winners';
    public function handle() {
        $tournaments = LotteryTournament::where('is_active', true)->where('end_date', '<', now())->where('prize_distributed', false)->get();
        foreach ($tournaments as $tournament) {
            $entries = $tournament->entries()->orderBy('total_win', 'desc')->get();
            $prizeDistribution = $tournament->prize_distribution ?: ['1' => 30, '2' => 20, '3' => 10];
            foreach ($entries as $index => $entry) {
                $rank = $index + 1;
                $percentage = $prizeDistribution[$rank] ?? 0;
                if ($percentage > 0) {
                    $prize = ($percentage / 100) * $tournament->prize_pool;
                    $entry->rank = $rank;
                    $entry->prize_awarded = $prize;
                    $entry->save();
                    $entry->user->wallet->increment('balance', $prize);
                }
            }
            $tournament->is_active = false;
            $tournament->prize_distributed = true;
            $tournament->save();
            $this->info("Tournament {$tournament->name} prizes distributed.");
        }
    }
}
