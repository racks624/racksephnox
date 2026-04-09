<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\CryptoPrice;
use App\Models\MachineInvestment;
use App\Models\FollowedTrader;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function index()
    {
        $user = Auth::user()->load('wallet', 'investments.plan', 'referrals', 'tradingAccount');

        $unreadNotificationsCount = $user->unreadNotifications->count();
        $latestNotifications = $user->notifications()->latest()->take(5)->get();

        $currency = $user->preferred_currency ?? session('currency', 'KES');

        $dashboardData = Cache::remember('dashboard_' . $user->id, 60, function () use ($user) {
            // Legacy investments
            $totalInvested = $user->investments->sum('amount');
            $totalProfit = $user->investments->sum('total_projected_profit');
            $activeInvestments = $user->investments()->where('status', 'active')->count();
            $completedInvestments = $user->investments()->where('status', 'completed')->count();

            // Machine investments (RX Series)
            $activeMachineInvestments = MachineInvestment::where('user_id', $user->id)
                ->where('status', 'active')
                ->with('machine')
                ->get()
                ->map(function ($inv) {
                    $inv->progress = $inv->progressPercentage();
                    $inv->days_left = $inv->daysRemaining();
                    $inv->current_profit = $inv->currentProfit();
                    $inv->wealth_tax_collected = $inv->wealth_tax_daily ?? 0;
                    return $inv;
                });

            $totalMachineInvested = $activeMachineInvestments->sum('amount');
            $totalMachineDailyProfit = $activeMachineInvestments->sum('daily_profit');
            $totalMachineProfitAccrued = $activeMachineInvestments->sum('profit_credited');
            $activeMachineCount = $activeMachineInvestments->count();

            // Financial stats
            $totalDeposited = $user->transactions()->where('type', 'deposit')->sum('amount');
            $totalWithdrawn = abs($user->transactions()->where('type', 'withdrawal')->sum('amount'));
            $totalInterest = $user->transactions()->where('type', 'interest')->sum('amount');
            $totalBonus = $user->transactions()->where('type', 'referral_bonus')->sum('amount');

            // Recent transactions
            $recentTransactions = $user->transactions()->latest()->take(10)->get();

            // Charts data
            $profitHistory = $this->getRealProfitHistory($user->id);
            $portfolio = $this->getPortfolioBreakdown($user->id);
            $weeklyPerformance = $this->getWeeklyPerformance($user->id);

            // Referral stats
            $referralCount = $user->referrals()->count();
            $referralLink = url('/refer/' . $user->referral_code);

            // ROI calculation
            $roi = $totalInvested > 0 ? round(($totalProfit / $totalInvested) * 100, 2) : 0;

            // Social trading preview (top 3 traders)
            $topTraders = FollowedTrader::with('trader.tradingProfile')
                ->where('follower_id', $user->id)
                ->take(3)
                ->get()
                ->map(function ($follow) {
                    return [
                        'name' => $follow->trader->name,
                        'username' => $follow->trader->tradingProfile->username ?? $follow->trader->name,
                        'copy_ratio' => $follow->copy_ratio,
                        'auto_copy' => $follow->auto_copy,
                    ];
                });

            return compact(
                'totalInvested', 'totalProfit', 'activeInvestments', 'completedInvestments',
                'totalDeposited', 'totalWithdrawn', 'totalInterest', 'totalBonus',
                'totalMachineInvested', 'totalMachineDailyProfit', 'totalMachineProfitAccrued',
                'activeMachineCount', 'activeMachineInvestments',
                'recentTransactions', 'profitHistory', 'portfolio', 'weeklyPerformance',
                'referralCount', 'referralLink', 'roi', 'topTraders'
            );
        });

        $cryptoPrices = CryptoPrice::latest()->take(5)->get();
        $btcPrice = $cryptoPrices->where('symbol', 'BTC')->first()->price_kes ?? 0;

        return view('dashboard', array_merge($dashboardData, compact(
            'user', 'cryptoPrices', 'unreadNotificationsCount', 'latestNotifications', 'currency', 'btcPrice'
        )));
    }

    private function getRealProfitHistory($userId)
    {
        $end = now();
        $start = now()->subDays(29)->startOfDay();

        $dailyProfits = Transaction::where('user_id', $userId)
            ->whereIn('type', ['interest', 'credit', 'machine_interest'])
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
        // Legacy investments
        $investments = \App\Models\Investment::where('user_id', $userId)
            ->where('status', 'active')
            ->with('plan')
            ->get();

        $breakdown = [];
        foreach ($investments as $inv) {
            $planName = $inv->plan->name ?? 'Legacy Plan';
            if (!isset($breakdown[$planName])) {
                $breakdown[$planName] = 0;
            }
            $breakdown[$planName] += $inv->amount;
        }

        // Machine investments
        $machineInvestments = MachineInvestment::where('user_id', $userId)
            ->where('status', 'active')
            ->with('machine')
            ->get();

        foreach ($machineInvestments as $inv) {
            $machineName = $inv->machine->name . ' (VIP ' . $inv->vip_level . ')';
            if (!isset($breakdown[$machineName])) {
                $breakdown[$machineName] = 0;
            }
            $breakdown[$machineName] += $inv->amount;
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
