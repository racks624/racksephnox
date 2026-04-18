<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LotteryGame;
use App\Models\LotterySpin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LotteryAnalyticsController extends Controller
{
    public function index()
    {
        $game = LotteryGame::where('is_active', true)->first();
        $rtpData = LotterySpin::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, SUM(win_amount) as total_wins, SUM(bet_amount) as total_bets')
            ->groupBy('date')
            ->get();
        $rtpChart = [
            'labels' => $rtpData->pluck('date'),
            'wins' => $rtpData->pluck('total_wins'),
            'bets' => $rtpData->pluck('total_bets'),
        ];

        $jackpotHistory = LotterySpin::where('created_at', '>=', now()->subDays(30))
            ->where('super_jackpot_hit', false)
            ->selectRaw('DATE(created_at) as date, SUM(tax_contribution) as daily_contribution')
            ->groupBy('date')
            ->get();

        $hourlySpins = LotterySpin::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $topUsers = LotterySpin::selectRaw('user_id, SUM(win_amount) as total_win')
            ->groupBy('user_id')
            ->orderBy('total_win', 'desc')
            ->with('user')
            ->take(10)
            ->get();

        return view('admin.lottery.analytics', compact('rtpChart', 'jackpotHistory', 'hourlySpins', 'topUsers', 'game'));
    }
}
