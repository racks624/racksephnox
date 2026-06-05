<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->transactions();
        if ($request->type) $query->where('type', $request->type);
        $transactions = $query->latest()->paginate(20);
        return view('transactions.index', compact('transactions'));
    }

    public function export()
    {
        $transactions = Auth::user()->transactions()->latest()->get();
        $csv = "ID,Type,Amount,Status,Description,Date\n";
        foreach ($transactions as $tx) {
            $csv .= "{$tx->id},{$tx->type},{$tx->amount},{$tx->status},\"{$tx->description}\",{$tx->created_at}\n";
        }
        return response($csv, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="transactions.csv"']);
    }
}
