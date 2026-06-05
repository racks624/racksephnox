<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WealthTaxController extends Controller
{
    public function history()
    {
        $user = Auth::user();
        $taxLogs = DB::table('wealth_tax_logs')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'date' => $log->created_at->format('Y-m-d'),
                    'frequency' => $log->frequency,
                    'profit_amount' => $log->profit_amount,
                    'tax_amount' => $log->tax_amount,
                    'tax_rate' => $log->tax_rate,
                ];
            });
        return response()->json(['success' => true, 'data' => $taxLogs]);
    }
}
