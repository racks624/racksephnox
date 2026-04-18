<?php

namespace App\Console\Commands;

use App\Models\LotteryTournament;
use App\Models\LotteryTournamentEntry;
use App\Models\User;
use Illuminate\Console\Command;

class DistributeTournamentPrizes extends Command
{
    protected $signature = 'lottery:distribute-prizes';
    protected $description = 'Distribute prizes to tournament winners';

    public function handle()
    {
        $tournaments = LotteryTournament::where('is_active', true)
            ->where('end_date', '<', now())
            ->where('prize_distributed', false)
            ->get();

        foreach ($tournaments as $tournament) {
            $this->info("Processing tournament: {$tournament->name}");
            $entries = $tournament->entries()->orderBy('total_win', 'desc')->get();
            $prizeDistribution = $tournament->prize_distribution ?: ['1' => 30, '2' => 20, '3' => 10];
            $totalPrizePool = $tournament->prize_pool;
            
            foreach ($entries as $index => $entry) {
                $rank = $index + 1;
                $percentage = $prizeDistribution[$rank] ?? 0;
                if ($percentage > 0) {
                    $prizeAmount = ($percentage / 100) * $totalPrizePool;
                    $entry->rank = $rank;
                    $entry->prize_awarded = $prizeAmount;
                    $entry->save();
                    
                    $user = $entry->user;
                    $user->wallet->increment('balance', $prizeAmount);
                    $user->transactions()->create([
                        'type' => 'tournament_prize',
                        'amount' => $prizeAmount,
                        'status' => 'completed',
                        'description' => "Tournament prize: {$tournament->name} (Rank #{$rank})",
                        'balance_after' => $user->wallet->balance,
                        'user_id' => $user->id,
                        'wallet_id' => $user->wallet->id,
                    ]);
                    $this->info("Awarded KES {$prizeAmount} to user {$user->id} (rank {$rank})");
                }
            }
            $tournament->is_active = false;
            $tournament->prize_distributed = true;
            $tournament->save();
        }
        
        $this->info('Tournament prize distribution completed.');
    }
}
