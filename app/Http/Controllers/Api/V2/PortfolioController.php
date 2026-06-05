<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\MachineInvestment;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{
    public function summary()
    {
        $user = Auth::user();
        $machineInvestments = MachineInvestment::where('user_id', $user->id)->with('machine')->get();
        $totalInvested = $machineInvestments->sum('amount');
        $totalProfitAccrued = $machineInvestments->sum('profit_credited');
        $activeInvestments = $machineInvestments->where('status', 'active')->values();

        return response()->json([
            'success' => true,
            'data' => [
                'total_invested' => $totalInvested,
                'total_profit_accrued' => $totalProfitAccrued,
                'active_investments_count' => $activeInvestments->count(),
                'active_investments' => $activeInvestments->map(function ($inv) {
                    return [
                        'machine' => $inv->machine->name,
                        'vip_level' => $inv->vip_level,
                        'amount' => $inv->amount,
                        'daily_profit' => $inv->daily_profit,
                        'progress' => $inv->progressPercentage(),
                        'days_remaining' => $inv->daysRemaining(),
                    ];
                }),
            ]
        ]);
    }
}
