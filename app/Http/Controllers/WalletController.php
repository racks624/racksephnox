<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Cache wallet and trading account for 1 minute
        $wallet = Cache::remember('wallet_' . $user->id, 60, function () use ($user) {
            return $user->wallet;
        });
        
        $tradingAccount = Cache::remember('trading_account_' . $user->id, 60, function () use ($user) {
            return $user->tradingAccount ?? $user->tradingAccount()->create(['balance' => 0]);
        });
        
        // All transactions with pagination
        $transactions = $user->transactions()->latest()->paginate(15);
        
        // Filtered transactions for tabs
        $deposits = $user->transactions()->where('type', 'deposit')->latest()->paginate(10);
        $withdrawals = $user->transactions()->where('type', 'withdrawal')->latest()->paginate(10);
        $interest = $user->transactions()->where('type', 'interest')->latest()->paginate(10);
        $referralBonuses = $user->transactions()->where('type', 'referral_bonus')->latest()->paginate(10);
        $tradingTransfers = $user->transactions()
            ->whereIn('type', ['trading_deposit', 'trading_withdrawal'])
            ->latest()->paginate(10);
        
        // Summary stats
        $totalDeposits = $user->transactions()->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = abs($user->transactions()->where('type', 'withdrawal')->sum('amount'));
        $totalInterest = $user->transactions()->where('type', 'interest')->sum('amount');
        $totalBonus = $user->transactions()->where('type', 'referral_bonus')->sum('amount');
        
        return view('wallet', compact(
            'wallet', 'tradingAccount', 'transactions', 'deposits', 'withdrawals',
            'interest', 'referralBonuses', 'tradingTransfers',
            'totalDeposits', 'totalWithdrawals', 'totalInterest', 'totalBonus'
        ));
    }
}
