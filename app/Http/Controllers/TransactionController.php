<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id)->with('wallet');

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $transactions = $query->paginate(15)->withQueryString();
        
        // Get unique transaction types for filter dropdown
        $types = Cache::remember('user_transaction_types_' . $user->id, 3600, function () use ($user) {
            return Transaction::where('user_id', $user->id)
                ->select('type')
                ->distinct()
                ->pluck('type');
        });

        // Summary stats
        $summary = Cache::remember('transaction_summary_' . $user->id, 300, function () use ($user) {
            return [
                'total_credits' => Transaction::where('user_id', $user->id)->where('amount', '>', 0)->sum('amount'),
                'total_debits' => abs(Transaction::where('user_id', $user->id)->where('amount', '<', 0)->sum('amount')),
                'total_transactions' => Transaction::where('user_id', $user->id)->count(),
            ];
        });

        return view('transactions.index', compact('transactions', 'types', 'summary'));
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id);

        // Apply same filters as index
        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('from')) $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to')) $query->whereDate('created_at', '<=', $request->to);
        if ($request->filled('min_amount')) $query->where('amount', '>=', $request->min_amount);
        if ($request->filled('max_amount')) $query->where('amount', '<=', $request->max_amount);
        if ($request->filled('search')) $query->where('description', 'like', '%' . $request->search . '%');

        $transactions = $query->latest()->get();

        $response = new StreamedResponse(function() use ($transactions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Date', 'Type', 'Description', 'Amount', 'Balance After', 'Reference']);
            foreach ($transactions as $tx) {
                fputcsv($handle, [
                    $tx->id,
                    $tx->created_at->format('Y-m-d H:i:s'),
                    $tx->type,
                    $tx->description,
                    $tx->amount,
                    $tx->balance_after,
                    $tx->reference,
                ]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transactions_' . date('Y-m-d') . '.csv"',
        ]);

        return $response;
    }
}
