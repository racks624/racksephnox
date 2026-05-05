<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\MachineInvestment;
use App\Models\LotteryGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Wealth pillars
        $walletBalance = $user->wallet?->balance ?? 0;
        $totalInvested = MachineInvestment::where('user_id', $user->id)->sum('amount');
        $totalProfit = MachineInvestment::where('user_id', $user->id)->sum('profit_credited');
        $activeInvestments = MachineInvestment::where('user_id', $user->id)->where('status', 'active')->count();
        $completedInvestments = MachineInvestment::where('user_id', $user->id)->where('status', 'completed')->count();

        // Banking stats
        $totalDeposited = Transaction::where('user_id', $user->id)->where('type', 'deposit')->sum('amount');
        $totalWithdrawn = abs(Transaction::where('user_id', $user->id)->where('type', 'withdrawal')->sum('amount'));
        $totalMachineInvested = MachineInvestment::where('user_id', $user->id)->sum('amount');
        $totalInterest = Transaction::where('user_id', $user->id)->where('type', 'interest')->sum('amount');

        // Recent transactions
        $recentTransactions = Transaction::where('user_id', $user->id)->latest()->take(10)->get();

        // Profit history (30 days)
        $profitLabels = collect(range(29, 0))->map(fn($i) => now()->subDays($i)->format('M d'))->toArray();
        $profitData = MachineInvestment::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->get()
            ->groupBy(fn($inv) => $inv->created_at->format('Y-m-d'))
            ->map(fn($group) => $group->sum('profit_credited'))
            ->values()
            ->toArray();
        $profitData = array_pad($profitData, 30, 0);
        $profitHistory = ['labels' => $profitLabels, 'data' => $profitData];

        // Weekly performance (volume)
        $weeklyLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $weeklyData = [];
        for ($i = 0; $i < 7; $i++) {
            $weeklyData[] = Transaction::where('user_id', $user->id)
                ->whereDate('created_at', now()->startOfWeek()->addDays($i))
                ->sum('amount');
        }
        $weeklyPerformance = ['labels' => $weeklyLabels, 'data' => $weeklyData];

        // Portfolio breakdown (by machine)
        $investmentsGrouped = MachineInvestment::where('user_id', $user->id)
            ->with('machine')
            ->get()
            ->groupBy(fn($inv) => $inv->machine?->name ?? 'Unknown');
        $portfolioLabels = $investmentsGrouped->keys()->toArray();
        $portfolioData = $investmentsGrouped->map(fn($group) => $group->sum('amount'))->values()->toArray();
        $portfolio = ['labels' => $portfolioLabels, 'data' => $portfolioData];

        // BTC price (cached)
        $btcPrice = Cache::remember('btc_price_kes', 60, fn() => rand(4500000, 5500000));

        // Referral stats
        $referralCount = $user->referrals()->count();
        $totalBonus = Transaction::where('user_id', $user->id)->where('type', 'referral_bonus')->sum('amount');

        // Notifications
        $unreadNotificationsCount = $user->unreadNotifications->count();

        // ROI
        $roi = $totalInvested > 0 ? round(($totalProfit / $totalInvested) * 100, 2) : 0;

        // Lottery progressive jackpot
        $lotteryJackpot = LotteryGame::where('is_active', true)->first()?->progressive_jackpot ?? 1000;

        return view('dashboard', compact(
            'user', 'walletBalance', 'totalInvested', 'totalProfit',
            'activeInvestments', 'completedInvestments', 'totalDeposited',
            'totalWithdrawn', 'totalMachineInvested', 'totalInterest',
            'recentTransactions', 'profitHistory', 'weeklyPerformance',
            'portfolio', 'btcPrice', 'referralCount', 'totalBonus',
            'unreadNotificationsCount', 'roi', 'lotteryJackpot'
            'lotteryJackpot' => LotteryGame::where('is_active', true)->first()?->progressive_jackpot ?? 1000,
        ));
    }
}
