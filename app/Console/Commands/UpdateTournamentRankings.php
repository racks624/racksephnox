<?php

namespace App\Console\Commands;

use App\Models\LotteryTournament;
use App\Models\LotteryTournamentEntry;
use Illuminate\Console\Command;

class UpdateTournamentRankings extends Command
{
    protected $signature = 'lottery:update-rankings';
    protected $description = 'Update tournament rankings based on total wins';

    public function handle()
    {
        $tournaments = LotteryTournament::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        foreach ($tournaments as $tournament) {
            $entries = $tournament->entries()->orderBy('total_win', 'desc')->get();
            foreach ($entries as $index => $entry) {
                $entry->rank = $index + 1;
                $entry->save();
            }
            $this->info("Updated rankings for tournament: {$tournament->name}");
        }
        $this->info('Tournament rankings updated.');
    }
}
