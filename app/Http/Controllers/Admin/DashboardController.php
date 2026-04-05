<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Investment;
use App\Models\Transaction;
use App\Models\DepositRequest;
use App\Models\WithdrawalRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $verifiedUsers = User::where('is_verified', true)->count();
        $totalInvested = Investment::where('status', 'active')->sum('amount');
        $pendingDeposits = DepositRequest::where('status', 'pending')->count();
        $pendingWithdrawals = WithdrawalRequest::where('status', 'pending')->count();
        $recentUsers = User::latest()->take(5)->get();
        
        return view('admin.dashboard', compact(
            'totalUsers', 'verifiedUsers', 'totalInvested',
            'pendingDeposits', 'pendingWithdrawals', 'recentUsers'
        ));
    }
}
