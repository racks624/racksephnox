<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\CryptoPrice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('wallet', 'investments.plan', 'referrals', 'tradingAccount');

        $unreadNotificationsCount = $user->unreadNotifications->count();
        $latestNotifications = $user->notifications()->latest()->take(5)->get();

        $dashboardData = Cache::remember('dashboard_' . $user->id, 60, function () use ($user) {
            $totalInvested = $user->investments->sum('amount');
            $totalProfit = $user->investments->sum('total_projected_profit');
            $activeInvestments = $user->investments()->where('status', 'active')->count();
            $completedInvestments = $user->investments()->where('status', 'completed')->count();

            $totalDeposited = $user->transactions()->where('type', 'deposit')->sum('amount');
            $totalWithdrawn = abs($user->transactions()->where('type', 'withdrawal')->sum('amount'));
            $totalInterest = $user->transactions()->where('type', 'interest')->sum('amount');
            $totalBonus = $user->transactions()->where('type', 'referral_bonus')->sum('amount');

            $totalMachineInvested = $user->machineInvestments()->sum('amount');
            $totalMachineProfit = $user->machineInvestments()->where('status', 'completed')->sum('total_return') - $totalMachineInvested;

            $recentTransactions = $user->transactions()->latest()->take(5)->get();

            $profitHistory = $this->getRealProfitHistory($user->id);
            $portfolio = $this->getPortfolioBreakdown($user->id);
            $weeklyPerformance = $this->getWeeklyPerformance($user->id);

            $referralCount = $user->referrals()->count();
            $referralLink = url('/refer/' . $user->referral_code);

            $roi = $totalInvested > 0 ? round(($totalProfit / $totalInvested) * 100, 2) : 0;

            return compact(
                'totalInvested', 'totalProfit', 'activeInvestments', 'completedInvestments',
                'totalDeposited', 'totalWithdrawn', 'totalInterest', 'totalBonus',
                'totalMachineInvested', 'totalMachineProfit',
                'recentTransactions', 'profitHistory', 'portfolio', 'weeklyPerformance',
                'referralCount', 'referralLink', 'roi'
            );
        });

        $cryptoPrices = CryptoPrice::latest()->take(5)->get();

        return view('dashboard', array_merge($dashboardData, compact(
            'user', 'cryptoPrices', 'unreadNotificationsCount', 'latestNotifications'
        )));
    }

    private function getRealProfitHistory($userId)
    {
        $end = now();
        $start = now()->subDays(29)->startOfDay();

        $dailyProfits = Transaction::where('user_id', $userId)
            ->whereIn('type', ['interest', 'credit'])
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $labels = [];
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = $date;
            $data[] = $dailyProfits[$date] ?? 0;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function getPortfolioBreakdown($userId)
    {
        $investments = \App\Models\Investment::where('user_id', $userId)
            ->where('status', 'active')
            ->with('plan')
            ->get();

        $breakdown = [];
        foreach ($investments as $inv) {
            $planName = $inv->plan->name;
            if (!isset($breakdown[$planName])) {
                $breakdown[$planName] = 0;
            }
            $breakdown[$planName] += $inv->amount;
        }
        return [
            'labels' => array_keys($breakdown),
            'data' => array_values($breakdown),
        ];
    }

    private function getWeeklyPerformance($userId)
    {
        $end = now();
        $start = now()->subDays(6)->startOfDay();

        $dailyTotals = Transaction::where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $labels = [];
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('D');
            $labels[] = $date;
            $data[] = $dailyTotals[now()->subDays($i)->format('Y-m-d')] ?? 0;
        }
        return ['labels' => $labels, 'data' => $data];
    }
}
