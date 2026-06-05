<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class LotteryAnalyticsController extends Controller
{
    public function index()
    {
        $analytics = new AnalyticsService();
        $rtpChart = $analytics->getRtpTrend(30);
        $jackpotHistory = \App\Models\LotterySpin::where('created_at', '>=', now()->subDays(30))
            ->where('super_jackpot_hit', false)
            ->selectRaw('DATE(created_at) as date, SUM(tax_contribution) as daily_contribution')
            ->groupBy('date')
            ->get();
        $hourlySpins = $analytics->getHourlySpinDistribution();
        $topUsers = $analytics->getTopWinners(10);
        $game = \App\Models\LotteryGame::where('is_active', true)->first();

        return view('admin.lottery.analytics', compact('rtpChart', 'jackpotHistory', 'hourlySpins', 'topUsers', 'game'));
    }
}
