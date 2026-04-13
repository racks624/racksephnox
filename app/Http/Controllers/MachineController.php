<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\MachineInvestment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MachineController extends Controller
{
    /**
     * Display a listing of all active machines.
     */
    public function index()
    {
        // Cache the list of active machines for 10 minutes
        $machines = Cache::remember('machines_active_list', 600, function () {
            return Machine::where('is_active', true)
                ->withCount('investments')
                ->get();
        });

        $user = Auth::user();
        $activeInvestments = collect();
        $totalInvested = 0;
        $totalProjectedProfit = 0;
        $totalEarnedProfit = 0;

        if ($user) {
            $activeInvestments = $user->machineInvestments()
                ->where('status', MachineInvestment::STATUS_ACTIVE)
                ->with('machine')
                ->get()
                ->map(function ($inv) {
                    $inv->progress_percentage = $inv->progressPercentage();
                    $inv->days_remaining = $inv->daysRemaining();
                    return $inv;
                });

            $totalInvested = $activeInvestments->sum('amount');
            $totalProjectedProfit = $activeInvestments->sum('total_return') - $totalInvested;
            $totalEarnedProfit = $activeInvestments->sum('profit_credited');
        }

        return view('machines.index', compact(
            'machines', 'activeInvestments', 'totalInvested',
            'totalProjectedProfit', 'totalEarnedProfit'
        ));
    }

    /**
     * Show a single machine details.
     */
    public function show($code)
    {
        $machine = Machine::where('code', $code)->where('is_active', true)->firstOrFail();
        $user = Auth::user();

        $activeInvestment = $user->machineInvestments()
            ->where('machine_id', $machine->id)
            ->where('status', MachineInvestment::STATUS_ACTIVE)
            ->first();

        if ($activeInvestment) {
            $activeInvestment->progress_percentage = $activeInvestment->progressPercentage();
            $activeInvestment->days_remaining = $activeInvestment->daysRemaining();
            $activeInvestment->current_profit = $activeInvestment->currentProfit();
        }

        $investmentHistory = $user->machineInvestments()
            ->where('machine_id', $machine->id)
            ->where('status', MachineInvestment::STATUS_COMPLETED)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $vipDetails = $machine->getVIPDetails();   // cached in model
        $statistics = $machine->getStatistics();   // cached in model

        return view('machines.show', compact(
            'machine', 'activeInvestment', 'investmentHistory',
            'vipDetails', 'statistics'
        ));
    }

    /**
     * Invest in a machine.
     */
    public function invest(Request $request, Machine $machine)
    {
        $request->validate([
            'vip_level' => 'required|in:1,2,3',
        ]);

        $vipLevel = (int) $request->vip_level;
        $amount = $machine->getStartAmountForVip($vipLevel);

        if (!$amount || $amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid VIP level or amount'
            ], 422);
        }

        $user = Auth::user();

        // Check existing active investment
        $existing = $user->machineInvestments()
            ->where('machine_id', $machine->id)
            ->where('status', MachineInvestment::STATUS_ACTIVE)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active investment in this machine.'
            ], 422);
        }

        // Check wallet balance
        if (!$user->wallet || $user->wallet->balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient wallet balance. Required: KES ' . number_format($amount, 2)
            ], 422);
        }

        try {
            DB::transaction(function () use ($user, $machine, $amount, $vipLevel) {
                // Debit wallet
                $user->wallet->decrement('balance', $amount);

                // Record transaction
                $user->transactions()->create([
                    'type'          => 'machine_investment',
                    'amount'        => -$amount,
                    'status'        => 'completed',
                    'description'   => "Investment in {$machine->name} - VIP {$vipLevel}",
                    'balance_after' => $user->wallet->balance,
                    'user_id'       => $user->id,
                    'wallet_id'     => $user->wallet->id,
                ]);

                // Calculate returns
                $dailyProfit = $machine->getDailyProfit($amount);
                $totalReturn = $machine->getTotalReturn($amount);
                $startDate = now();
                $endDate = $startDate->copy()->addDays($machine->duration_days);

                // Create investment record
                MachineInvestment::create([
                    'user_id'       => $user->id,
                    'machine_id'    => $machine->id,
                    'vip_level'     => $vipLevel,
                    'amount'        => $amount,
                    'daily_profit'  => $dailyProfit,
                    'total_return'  => $totalReturn,
                    'start_date'    => $startDate,
                    'end_date'      => $endDate,
                    'status'        => MachineInvestment::STATUS_ACTIVE,
                    'profit_credited' => 0,
                ]);
            });

            // Clear caches
            Cache::forget('machines_active_list');
            Cache::forget("machine_vip_details_{$machine->id}");
            Cache::forget("machine_statistics_{$machine->id}");
            Cache::forget('dashboard_' . $user->id);

            return response()->json([
                'success' => true,
                'message' => 'Investment successful! Your daily profit will start accruing tomorrow.',
                'data' => [
                    'amount'       => $amount,
                    'daily_profit' => $dailyProfit ?? 0,
                    'total_return' => $totalReturn ?? 0,
                    'end_date'     => $endDate->format('Y-m-d'),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Investment failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Investment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get status of a specific investment (AJAX).
     */
    public function status(MachineInvestment $investment)
    {
        if ($investment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id'                  => $investment->id,
                'amount'              => $investment->amount,
                'daily_profit'        => $investment->daily_profit,
                'current_profit'      => $investment->currentProfit(),
                'total_return'        => $investment->total_return,
                'profit_credited'     => $investment->profit_credited,
                'start_date'          => $investment->start_date->format('Y-m-d'),
                'end_date'            => $investment->end_date->format('Y-m-d'),
                'days_elapsed'        => $investment->daysElapsed(),
                'days_remaining'      => $investment->daysRemaining(),
                'progress_percentage' => $investment->progressPercentage(),
                'status'              => $investment->status,
            ]
        ]);
    }

    /**
     * Get all investments of the authenticated user (JSON).
     */
    public function myInvestments()
    {
        $user = Auth::user();
        $investments = MachineInvestment::where('user_id', $user->id)
            ->with('machine')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($inv) {
                return [
                    'id'              => $inv->id,
                    'machine_name'    => $inv->machine->name,
                    'machine_code'    => $inv->machine->code,
                    'vip_level'       => $inv->vip_level,
                    'amount'          => $inv->amount,
                    'daily_profit'    => $inv->daily_profit,
                    'total_return'    => $inv->total_return,
                    'profit_credited' => $inv->profit_credited,
                    'status'          => $inv->status,
                    'start_date'      => $inv->start_date->format('Y-m-d'),
                    'end_date'        => $inv->end_date->format('Y-m-d'),
                    'progress'        => $inv->progressPercentage(),
                    'days_remaining'  => $inv->daysRemaining(),
                ];
            });

        return response()->json(['success' => true, 'data' => $investments]);
    }

    /**
     * Early withdrawal from a machine investment.
     */
    public function earlyWithdraw(MachineInvestment $investment)
    {
        if ($investment->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (!$investment->isActive()) {
            return response()->json(['success' => false, 'message' => 'Investment is not active'], 422);
        }

        $penaltyRate = $investment->machine->early_withdrawal_penalty ?? 20;
        $refundAmount = $investment->amount * (1 - $penaltyRate / 100);

        try {
            DB::transaction(function () use ($investment, $refundAmount, $penaltyRate) {
                $wallet = $investment->user->wallet;
                $wallet->increment('balance', $refundAmount);

                $investment->user->transactions()->create([
                    'type'          => 'machine_early_withdrawal',
                    'amount'        => $refundAmount,
                    'status'        => 'completed',
                    'description'   => "Early withdrawal from {$investment->machine->name} (Penalty: {$penaltyRate}%)",
                    'balance_after' => $wallet->balance,
                    'user_id'       => $investment->user_id,
                    'wallet_id'     => $wallet->id,
                ]);

                $investment->status = 'cancelled';
                $investment->save();
            });

            // Clear dashboard cache
            Cache::forget('dashboard_' . $investment->user_id);

            return response()->json([
                'success' => true,
                'message' => "Early withdrawal processed. Refund: KES " . number_format($refundAmount, 2),
                'refund_amount' => $refundAmount,
                'penalty' => $penaltyRate,
            ]);
        } catch (\Exception $e) {
            Log::error('Early withdrawal failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Withdrawal failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Global statistics for all machines (AJAX).
     */
    public function globalStats()
    {
        $stats = Cache::remember('machines_global_stats', 300, function () {
            $machines = Machine::where('is_active', true)->get();
            return [
                'total_machines'     => $machines->count(),
                'total_invested'     => $machines->sum(fn($m) => $m->investments()->sum('amount')),
                'active_investments' => $machines->sum(fn($m) => $m->activeInvestments()->count()),
                'total_investors'    => MachineInvestment::distinct('user_id')->count('user_id'),
            ];
        });

        return response()->json(['success' => true, 'data' => $stats]);
    }
}
