<?php

namespace App\Services\Investment;

use App\Models\Investment;
use App\Models\Machine;
use App\Models\InvestmentPlan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnifiedInvestmentService
{
    /**
     * Get all investments (both legacy and RX machines) for a user
     */
    public function getAllInvestments(User $user)
    {
        $legacyInvestments = $user->investments()
            ->with('plan')
            ->whereNull('machine_id')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($inv) {
                return [
                    'type' => 'legacy',
                    'id' => $inv->id,
                    'name' => $inv->plan->name ?? 'Legacy Plan',
                    'amount' => $inv->amount,
                    'daily_profit' => $inv->daily_profit,
                    'total_return' => $inv->total_projected_profit,
                    'status' => $inv->status,
                    'start_date' => $inv->start_date,
                    'end_date' => $inv->end_date,
                    'progress' => $inv->progressPercentage(),
                ];
            });

        $machineInvestments = $user->machineInvestments()
            ->with('machine')
            ->get()
            ->map(function ($inv) {
                return [
                    'type' => 'machine',
                    'id' => $inv->id,
                    'name' => $inv->machine->name,
                    'vip_level' => $inv->vip_level,
                    'amount' => $inv->amount,
                    'daily_profit' => $inv->daily_profit,
                    'total_return' => $inv->total_return,
                    'profit_credited' => $inv->profit_credited,
                    'status' => $inv->status,
                    'start_date' => $inv->start_date,
                    'end_date' => $inv->end_date,
                    'progress' => $inv->progressPercentage(),
                    'days_remaining' => $inv->daysRemaining(),
                ];
            });

        return $legacyInvestments->concat($machineInvestments);
    }

    /**
     * Get total invested amount (legacy + machines)
     */
    public function getTotalInvested(User $user): float
    {
        $legacyTotal = $user->investments()->sum('amount');
        $machineTotal = $user->machineInvestments()->sum('amount');
        return $legacyTotal + $machineTotal;
    }

    /**
     * Get total profit earned (legacy + machines)
     */
    public function getTotalProfit(User $user): float
    {
        $legacyProfit = $user->transactions()->where('type', 'interest')->sum('amount');
        $machineProfit = $user->machineInvestments()->sum('profit_credited');
        return $legacyProfit + $machineProfit;
    }
}
