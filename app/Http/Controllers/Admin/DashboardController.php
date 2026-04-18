<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DepositRequest;
use App\Models\WithdrawalRequest;
use App\Models\LotterySpin;
use App\Models\LotteryGame;
use App\Models\LotteryRevenueTarget;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('admin_dashboard_stats', 300, function () {
            return [
                'total_users' => User::count(),
                'new_users_today' => User::whereDate('created_at', today())->count(),
                'active_users' => User::where('last_activity_at', '>=', now()->subMinutes(15))->count(),
                'total_invested' => 0,
                'pending_deposits' => DepositRequest::where('status', 'pending')->count(),
                'pending_deposits_amount' => DepositRequest::where('status', 'pending')->sum('amount'),
                'pending_withdrawals' => WithdrawalRequest::where('status', 'pending')->count(),
                'pending_withdrawals_amount' => WithdrawalRequest::where('status', 'pending')->sum('amount'),
                'total_withdrawn' => WithdrawalRequest::where('status', 'completed')->sum('amount'),
                'total_deposited' => DepositRequest::where('status', 'verified')->sum('amount'),
                'total_referrals' => 0,
                'total_referral_bonus' => 0,
                'total_lottery_spins' => LotterySpin::count(),
                'total_lottery_bets' => LotterySpin::sum('bet_amount'),
                'total_lottery_wins' => LotterySpin::sum('win_amount'),
                'jackpot_hits' => LotterySpin::where('super_jackpot_hit', true)->count(),
                'total_trading_volume' => 0,
                'active_machines_count' => 0,
            ];
        });

        // Revenue target (88,000 KES / 26 days)
        $revenueTarget = LotteryRevenueTarget::where('is_active', true)->first();
        if (!$revenueTarget) {
            $revenueTarget = LotteryRevenueTarget::create([
                'target_amount' => 88000,
                'current_revenue' => 0,
                'start_date' => now(),
                'end_date' => now()->addDays(26),
            ]);
        }
        $targetProgress = ($revenueTarget->current_revenue / $revenueTarget->target_amount) * 100;
        $daysLeft = max(0, now()->diffInDays($revenueTarget->end_date, false));
        $remaining = $revenueTarget->target_amount - $revenueTarget->current_revenue;

        // Chart data (30 days)
        $userGrowth = ['labels' => [], 'data' => []];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $userGrowth['labels'][] = $date->format('M d');
            $userGrowth['data'][] = User::whereDate('created_at', '<=', $date)->count();
        }

        $revenueTrend = ['labels' => [], 'data' => []];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenueTrend['labels'][] = $date->format('M d');
            $revenueTrend['data'][] = DepositRequest::where('status', 'verified')->whereDate('verified_at', $date)->sum('amount');
        }

        $lotteryActivity = ['labels' => [], 'spins' => [], 'bets' => []];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $lotteryActivity['labels'][] = $date->format('M d');
            $lotteryActivity['spins'][] = LotterySpin::whereDate('created_at', $date)->count();
            $lotteryActivity['bets'][] = LotterySpin::whereDate('created_at', $date)->sum('bet_amount');
        }

        $recentActivities = collect();
        $recentActivities = $recentActivities->concat(
            DepositRequest::with('user')->latest()->take(5)->get()->map(fn($d) => [
                'type' => 'deposit', 'user' => $d->user->name, 'amount' => $d->amount, 'status' => $d->status, 'created_at' => $d->created_at,
            ])
        )->concat(
            WithdrawalRequest::with('user')->latest()->take(5)->get()->map(fn($w) => [
                'type' => 'withdrawal', 'user' => $w->user->name, 'amount' => $w->amount, 'status' => $w->status, 'created_at' => $w->created_at,
            ])
        )->concat(
            LotterySpin::with('user')->latest()->take(5)->get()->map(fn($s) => [
                'type' => 'lottery', 'user' => $s->user->name, 'bet' => $s->bet_amount, 'win' => $s->win_amount, 'created_at' => $s->created_at,
            ])
        )->sortByDesc('created_at')->take(10);

        $jackpot = LotteryGame::where('is_active', true)->first()?->progressive_jackpot ?? 1000;

        return view('admin.dashboard', compact(
            'stats', 'userGrowth', 'revenueTrend', 'lotteryActivity', 'recentActivities', 'jackpot',
            'revenueTarget', 'targetProgress', 'daysLeft', 'remaining'
        ));
    }

    public function stats()
    {
        Cache::forget('admin_dashboard_stats');
        $stats = Cache::remember('admin_dashboard_stats', 300, function () {
            return [
                'total_users' => User::count(),
                'new_users_today' => User::whereDate('created_at', today())->count(),
                'active_users' => User::where('last_activity_at', '>=', now()->subMinutes(15))->count(),
                'total_invested' => 0,
                'pending_deposits' => DepositRequest::where('status', 'pending')->count(),
                'pending_withdrawals' => WithdrawalRequest::where('status', 'pending')->count(),
                'total_lottery_spins' => LotterySpin::count(),
                'jackpot' => LotteryGame::where('is_active', true)->first()?->progressive_jackpot ?? 1000,
            ];
        });
        return response()->json($stats);
    }
}
