<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function balance(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'success' => true,
            'balance' => $user->wallet->balance ?? 0,
            'currency' => 'KES',
        ]);
    }

    public function transactions(Request $request)
    {
        $user = $request->user();
        $transactions = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $transactions->items(),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ]
        ]);
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:to_trading,to_wallet',
        ]);

        $user = $request->user();
        
        if ($request->type === 'to_trading') {
            if ($user->wallet->balance < $request->amount) {
                return response()->json(['success' => false, 'message' => 'Insufficient balance'], 422);
            }
            $user->wallet->decrement('balance', $request->amount);
            $user->tradingAccount->increment('balance', $request->amount);
            
            $user->transactions()->create([
                'type' => 'transfer_to_trading',
                'amount' => -$request->amount,
                'status' => 'completed',
                'description' => 'Transfer to trading account',
                'balance_after' => $user->wallet->balance,
            ]);
        } else {
            if ($user->tradingAccount->balance < $request->amount) {
                return response()->json(['success' => false, 'message' => 'Insufficient trading balance'], 422);
            }
            $user->tradingAccount->decrement('balance', $request->amount);
            $user->wallet->increment('balance', $request->amount);
            
            $user->transactions()->create([
                'type' => 'transfer_to_wallet',
                'amount' => $request->amount,
                'status' => 'completed',
                'description' => 'Transfer from trading to wallet',
                'balance_after' => $user->wallet->balance,
            ]);
        }
        
        return response()->json(['success' => true, 'message' => 'Transfer completed']);
    }

    public function summary(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'wallet_balance' => $user->wallet->balance ?? 0,
                'trading_balance' => $user->tradingAccount->balance ?? 0,
                'total_deposited' => $user->transactions()->where('type', 'deposit')->sum('amount'),
                'total_withdrawn' => abs($user->transactions()->where('type', 'withdrawal')->sum('amount')),
                'total_interest' => $user->transactions()->where('type', 'interest')->sum('amount'),
            ]
        ]);
    }
}
