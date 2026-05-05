<?php

namespace App\Services;

use App\Models\User;
use App\Models\Investment;
use App\Models\MachineInvestment;
use App\Models\Transaction;
use App\Models\LotterySpin;
use App\Models\LotteryGame;
use App\Models\TradingOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    // Cache all stats for 5 minutes
    public function getAdminStats()
    {
        return Cache::remember('admin_dashboard_stats', 300, function () {
            return [
                'total_users' => User::count(),
                'new_users_today' => User::whereDate('created_at', today())->count(),
                'active_users' => User::where('last_activity_at', '>=', now()->subMinutes(15))->count(),
                'total_invested' => Investment::sum('amount') + MachineInvestment::sum('amount'),
                'pending_deposits' => \App\Models\DepositRequest::where('status', 'pending')->count(),
                'pending_withdrawals' => \App\Models\WithdrawalRequest::where('status', 'pending')->count(),
                'total_lottery_spins' => LotterySpin::count(),
                'total_lottery_bets' => LotterySpin::sum('bet_amount'),
                'total_lottery_wins' => LotterySpin::sum('win_amount'),
                'jackpot_hits' => LotterySpin::where('super_jackpot_hit', true)->count(),
                'total_trading_volume' => TradingOrder::sum('amount'),
                'active_machines_count' => MachineInvestment::where('status', 'active')->count(),
            ];
        });
    }

    public function getUserGrowth(int $days = 30)
    {
        $labels = [];
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            $data[] = User::whereDate('created_at', '<=', $date)->count();
        }
        return ['labels' => $labels, 'data' => $data];
    }

    public function getRevenueTrend(int $days = 30)
    {
        $labels = [];
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            $data[] = \App\Models\DepositRequest::where('status', 'verified')
                        ->whereDate('verified_at', $date)
                        ->sum('amount');
        }
        return ['labels' => $labels, 'data' => $data];
    }

    public function getLotteryActivity(int $days = 30)
    {
        $labels = [];
        $spins = [];
        $bets = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            $spins[] = LotterySpin::whereDate('created_at', $date)->count();
            $bets[] = LotterySpin::whereDate('created_at', $date)->sum('bet_amount');
        }
        return ['labels' => $labels, 'spins' => $spins, 'bets' => $bets];
    }

    public function getRtpTrend(int $days = 30)
    {
        $rtpData = LotterySpin::where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, SUM(win_amount) as total_wins, SUM(bet_amount) as total_bets')
            ->groupBy('date')
            ->get();
        return [
            'labels' => $rtpData->pluck('date'),
            'wins' => $rtpData->pluck('total_wins'),
            'bets' => $rtpData->pluck('total_bets'),
        ];
    }

    public function getHourlySpinDistribution()
    {
        return LotterySpin::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    public function getTopWinners($limit = 10)
    {
        return LotterySpin::selectRaw('user_id, SUM(win_amount) as total_win')
            ->groupBy('user_id')
            ->orderBy('total_win', 'desc')
            ->with('user')
            ->take($limit)
            ->get();
    }

    public function getTradingVolumeTrend(int $days = 30)
    {
        $labels = [];
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            $data[] = TradingOrder::whereDate('created_at', $date)->sum('amount');
        }
        return ['labels' => $labels, 'data' => $data];
    }

    public function getMachineInvestmentTrend(int $days = 30)
    {
        $labels = [];
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            $data[] = MachineInvestment::whereDate('created_at', $date)->sum('amount');
        }
        return ['labels' => $labels, 'data' => $data];
    }

    public function getUserLotteryStats($userId)
    {
        return Cache::remember("user_lottery_stats_{$userId}", 600, function () use ($userId) {
            $spins = LotterySpin::where('user_id', $userId);
            return [
                'total_spins' => $spins->count(),
                'total_bet' => $spins->sum('bet_amount'),
                'total_win' => $spins->sum('win_amount'),
                'mini_jackpots' => $spins->where('mini_jackpot_hit', true)->count(),
                'super_jackpots' => $spins->where('super_jackpot_hit', true)->count(),
                'net_profit' => $spins->sum('win_amount') - $spins->sum('bet_amount'),
            ];
        });
    }
}
