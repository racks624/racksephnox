<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Investment;
use App\Models\MachineInvestment;
use App\Models\DepositRequest;
use App\Models\WithdrawalRequest;
use App\Models\LotterySpin;
use App\Models\LotteryGame;
use App\Models\Transaction;
use App\Models\Referral;
use App\Models\TradingOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard with live stats and charts.
     */
    public function index()
    {
        // Cache stats for 5 minutes to reduce DB load
        $stats = Cache::remember('admin_dashboard_stats', 300, function () {
            return [
                'total_users' => User::count(),
                'new_users_today' => User::whereDate('created_at', today())->count(),
                'active_users' => User::where('last_activity_at', '>=', now()->subMinutes(15))->count(),
                'total_invested' => Investment::sum('amount') + MachineInvestment::sum('amount'),
                'pending_deposits' => DepositRequest::where('status', 'pending')->count(),
                'pending_deposits_amount' => DepositRequest::where('status', 'pending')->sum('amount'),
                'pending_withdrawals' => WithdrawalRequest::where('status', 'pending')->count(),
                'pending_withdrawals_amount' => WithdrawalRequest::where('status', 'pending')->sum('amount'),
                'total_withdrawn' => WithdrawalRequest::where('status', 'completed')->sum('amount'),
                'total_deposited' => DepositRequest::where('status', 'verified')->sum('amount'),
                'total_referrals' => Referral::count(),
                'total_referral_bonus' => Transaction::where('type', 'referral_bonus')->sum('amount'),
                'total_lottery_spins' => LotterySpin::count(),
                'total_lottery_bets' => LotterySpin::sum('bet_amount'),
                'total_lottery_wins' => LotterySpin::sum('win_amount'),
                'jackpot_hits' => LotterySpin::whereRaw('json_extract(result, "$.jackpot_hit") = true')->count(),
                'total_trading_volume' => TradingOrder::sum('amount'),
                'active_machines_count' => MachineInvestment::where('status', 'active')->count(),
            ];
        });

        // Chart data – user growth (last 30 days)
        $userGrowth = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $userGrowth['labels'][] = $date->format('M d');
            $userGrowth['data'][] = User::whereDate('created_at', '<=', $date)->count();
        }

        // Chart data – revenue trend (last 30 days from deposits)
        $revenueTrend = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenueTrend['labels'][] = $date->format('M d');
            $revenueTrend['data'][] = DepositRequest::where('status', 'verified')
                ->whereDate('verified_at', $date)
                ->sum('amount');
        }

        // Chart data – lottery activity (last 30 days)
        $lotteryActivity = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $lotteryActivity['labels'][] = $date->format('M d');
            $lotteryActivity['spins'][] = LotterySpin::whereDate('created_at', $date)->count();
            $lotteryActivity['bets'][] = LotterySpin::whereDate('created_at', $date)->sum('bet_amount');
        }

        // Recent activities (deposits, withdrawals, lottery spins, investments)
        $recentActivities = collect();
        $recentActivities = $recentActivities->concat(
            DepositRequest::with('user')->latest()->take(5)->get()->map(fn($d) => [
                'type' => 'deposit',
                'user' => $d->user->name,
                'amount' => $d->amount,
                'status' => $d->status,
                'created_at' => $d->created_at,
            ])
        )->concat(
            WithdrawalRequest::with('user')->latest()->take(5)->get()->map(fn($w) => [
                'type' => 'withdrawal',
                'user' => $w->user->name,
                'amount' => $w->amount,
                'status' => $w->status,
                'created_at' => $w->created_at,
            ])
        )->concat(
            LotterySpin::with('user')->latest()->take(5)->get()->map(fn($s) => [
                'type' => 'lottery',
                'user' => $s->user->name,
                'bet' => $s->bet_amount,
                'win' => $s->win_amount,
                'created_at' => $s->created_at,
            ])
        )->sortByDesc('created_at')->take(10);

        // Current progressive jackpot
        $jackpot = LotteryGame::where('is_active', true)->first()?->progressive_jackpot ?? 1000;

        return view('admin.dashboard', compact(
            'stats',
            'userGrowth',
            'revenueTrend',
            'lotteryActivity',
            'recentActivities',
            'jackpot'
        ));
    }

    /**
     * API endpoint for refreshing stats via AJAX (used by live dashboard).
     */
    public function stats()
    {
        Cache::forget('admin_dashboard_stats');
        $stats = Cache::remember('admin_dashboard_stats', 300, function () {
            return [
                'total_users' => User::count(),
                'new_users_today' => User::whereDate('created_at', today())->count(),
                'active_users' => User::where('last_activity_at', '>=', now()->subMinutes(15))->count(),
                'total_invested' => Investment::sum('amount') + MachineInvestment::sum('amount'),
                'pending_deposits' => DepositRequest::where('status', 'pending')->count(),
                'pending_withdrawals' => WithdrawalRequest::where('status', 'pending')->count(),
                'total_lottery_spins' => LotterySpin::count(),
                'jackpot' => LotteryGame::where('is_active', true)->first()?->progressive_jackpot ?? 1000,
            ];
        });
        return response()->json($stats);
    }
}
