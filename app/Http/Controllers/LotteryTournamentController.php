<?php
namespace App\Http\Controllers;
use App\Models\LotteryTournament;
class LotteryTournamentController extends Controller
{
    public function index() {
        $activeTournament = LotteryTournament::where('is_active', true)
            ->where('start_date', '<=', now())->where('end_date', '>=', now())->first();
        $upcoming = LotteryTournament::where('start_date', '>', now())->orderBy('start_date')->get();
        $past = LotteryTournament::where('end_date', '<', now())->orderBy('end_date', 'desc')->take(5)->get();
        $leaderboard = $activeTournament ? $activeTournament->entries()->with('user')->orderBy('total_win', 'desc')->take(20)->get() : [];
        return view('lottery.tournaments', compact('activeTournament', 'upcoming', 'past', 'leaderboard'));
    }
}
