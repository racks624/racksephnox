<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Get all transactions for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $user->transactions();
        
        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by date range
        if ($request->has('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->has('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        
        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $transactions->items(),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
            'summary' => [
                'total_deposits' => $user->transactions()->where('type', 'deposit')->sum('amount'),
                'total_withdrawals' => abs($user->transactions()->where('type', 'withdrawal')->sum('amount')),
                'total_interest' => $user->transactions()->where('type', 'interest')->sum('amount'),
                'total_bonus' => $user->transactions()->where('type', 'referral_bonus')->sum('amount'),
            ]
        ]);
    }

    /**
     * Export transactions to CSV
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $transactions = $user->transactions()->orderBy('created_at', 'desc')->get();
        
        $csv = "ID,Type,Amount,Status,Description,Date\n";
        foreach ($transactions as $tx) {
            $csv .= implode(',', [
                $tx->id,
                $tx->type,
                $tx->amount,
                $tx->status,
                '"' . str_replace('"', '""', $tx->description ?? '') . '"',
                $tx->created_at,
            ]) . "\n";
        }
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transactions.csv"',
        ]);
    }

    /**
     * Get transaction summary
     */
    public function summary()
    {
        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'total_deposits' => $user->transactions()->where('type', 'deposit')->sum('amount'),
                'total_withdrawals' => abs($user->transactions()->where('type', 'withdrawal')->sum('amount')),
                'total_interest' => $user->transactions()->where('type', 'interest')->sum('amount'),
                'total_bonus' => $user->transactions()->where('type', 'referral_bonus')->sum('amount'),
                'total_invested' => $user->machineInvestments()->sum('amount'),
                'total_profit' => $user->machineInvestments()->sum('profit_credited'),
            ]
        ]);
    }

    /**
     * Get transaction types
     */
    public function types()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'deposit',
                'withdrawal',
                'interest',
                'referral_bonus',
                'machine_investment',
                'machine_interest',
                'machine_early_withdrawal',
                'bonus',
            ]
        ]);
    }
}
