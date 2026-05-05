<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Investment;
use App\Models\Transaction;
use App\Models\DepositRequest;
use App\Models\WithdrawalRequest;
use App\Models\MachineInvestment;
use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'verified_users' => User::where('is_verified', true)->count(),
            'total_invested' => Investment::sum('amount') + MachineInvestment::sum('amount'),
            'total_machine_invested' => MachineInvestment::sum('amount'),
            'pending_deposits' => DepositRequest::where('status', 'pending')->count(),
            'pending_withdrawals' => WithdrawalRequest::where('status', 'pending')->count(),
            'total_interest_paid' => Transaction::where('type', 'interest')->sum('amount'),
            'total_referral_bonus' => Transaction::where('type', 'referral_bonus')->sum('amount'),
            'total_machines' => Machine::where('is_active', true)->count(),
            'rx0_active' => Machine::where('code', 'RX0')->where('is_active', true)->exists(),
        ];

        $userGrowth = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $investmentVolume = Transaction::selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->whereIn('type', ['deposit', 'machine_investment', 'investment'])
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $recentUsers = User::latest()->take(10)->get();
        $recentDeposits = DepositRequest::with('user')->latest()->take(5)->get();
        $recentWithdrawals = WithdrawalRequest::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'stats', 'userGrowth', 'investmentVolume',
            'recentUsers', 'recentDeposits', 'recentWithdrawals'
        ));
    }

    public function stats()
    {
        $stats = Cache::remember('admin_dashboard_stats', 60, function () {
            return [
                'total_users' => User::count(),
                'new_users_today' => User::whereDate('created_at', today())->count(),
                'active_investments' => MachineInvestment::where('status', 'active')->count(),
                'total_pnl' => Transaction::where('type', 'interest')->sum('amount'),
                'daily_volume' => Transaction::whereDate('created_at', today())->sum('amount'),
                'machines_count' => Machine::where('is_active', true)->count(),
            ];
        });
        return response()->json($stats);
    }
}
