<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LotterySpin;
use App\Models\LotteryTournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $wallet = $user->wallet;
        
        // Lottery stats
        $lotteryStats = [
            'total_spins' => LotterySpin::where('user_id', $user->id)->count(),
            'total_bet' => LotterySpin::where('user_id', $user->id)->sum('bet_amount'),
            'total_win' => LotterySpin::where('user_id', $user->id)->sum('win_amount'),
            'mini_jackpots' => LotterySpin::where('user_id', $user->id)->where('mini_jackpot_hit', true)->count(),
            'super_jackpots' => LotterySpin::where('user_id', $user->id)->where('super_jackpot_hit', true)->count(),
            'free_spins_available' => $user->free_spins_available ?? 0,
        ];
        
        // Active tournament rank
        $activeTournament = LotteryTournament::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
        $tournamentRank = null;
        if ($activeTournament) {
            $rankEntry = $activeTournament->entries()
                ->where('user_id', $user->id)
                ->first();
            $tournamentRank = $rankEntry ? $rankEntry->rank : null;
        }
        
        // Existing dashboard data (profits, investments, transactions, etc.)
        // ... (keep your existing logic for $totalInvested, $roi, $totalProfit, $activeInvestments, $completedInvestments, $totalDeposited, $totalWithdrawn, $totalMachineInvested, $totalInterest, $recentTransactions, $profitHistory, $weeklyPerformance, $portfolio, $btcPrice, $user->tradingAccount, $unreadNotificationsCount, $referralCount, $totalBonus, $totalReferralEarnings)
        // For brevity, I include a simplified version; you should merge with your existing code.
        
        $user = Auth::user();
        $totalInvested = $user->investments()->sum('amount') + $user->machineInvestments()->sum('amount');
        $roi = 0;
        $totalProfit = $user->transactions()->where('type', 'interest')->sum('amount');
        $activeInvestments = $user->investments()->where('status', 'active')->count() + $user->machineInvestments()->where('status', 'active')->count();
        $completedInvestments = $user->investments()->where('status', 'completed')->count() + $user->machineInvestments()->where('status', 'completed')->count();
        $totalDeposited = $user->transactions()->where('type', 'deposit')->sum('amount');
        $totalWithdrawn = $user->transactions()->where('type', 'withdrawal')->sum('amount');
        $totalMachineInvested = $user->machineInvestments()->sum('amount');
        $totalInterest = $user->transactions()->where('type', 'interest')->sum('amount');
        $recentTransactions = $user->transactions()->latest()->take(10)->get();
        $profitHistory = ['labels' => [], 'data' => []];
        $weeklyPerformance = ['labels' => [], 'data' => []];
        $portfolio = null;
        $btcPrice = 0;
        $unreadNotificationsCount = $user->unreadNotifications()->count();
        $referralCount = $user->referrals()->count();
        $totalBonus = $user->transactions()->where('type', 'referral_bonus')->sum('amount');
        $totalReferralEarnings = $totalBonus;
        
        return view('dashboard', compact(
            'totalInvested', 'roi', 'totalProfit', 'activeInvestments', 'completedInvestments',
            'totalDeposited', 'totalWithdrawn', 'totalMachineInvested', 'totalInterest',
            'recentTransactions', 'profitHistory', 'weeklyPerformance', 'portfolio',
            'btcPrice', 'user', 'unreadNotificationsCount', 'referralCount', 'totalBonus',
            'totalReferralEarnings', 'lotteryStats', 'tournamentRank'
        ));
    }
}
