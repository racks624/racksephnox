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
    public function index()
    {
        $machines = Cache::remember('machines_active', 300, function () {
            return Machine::where('is_active', true)->get();
        });

        $user = Auth::user();
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

        return view('machines.index', compact(
            'machines', 'activeInvestments', 'totalInvested', 
            'totalProjectedProfit', 'totalEarnedProfit'
        ));
    }

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

        $vipDetails = $machine->getVIPDetails();
        $statistics = $machine->getStatistics();

        return view('machines.show', compact(
            'machine', 'activeInvestment', 'investmentHistory', 
            'vipDetails', 'statistics'
        ));
    }

    public function invest(Request $request, Machine $machine)
    {
        $request->validate([
            'vip_level' => 'required|in:1,2,3',
        ]);

        $vipLevel = (int) $request->vip_level;
        $amount = $machine->getStartAmountForVip($vipLevel);
        $user = Auth::user();

        // Check if user already has an active investment in this machine
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
        if ($user->wallet->balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient wallet balance. Please deposit funds.'
            ], 422);
        }

        try {
            DB::transaction(function () use ($user, $machine, $amount, $vipLevel) {
                // Debit wallet
                $user->wallet->decrement('balance', $amount);
                
                // Record transaction
                $user->transactions()->create([
                    'type' => 'machine_investment',
                    'amount' => -$amount,
                    'status' => 'completed',
                    'description' => "Investment in {$machine->name} - VIP {$vipLevel}",
                    'balance_after' => $user->wallet->balance,
                ]);

                // Calculate returns
                $dailyProfit = $machine->getDailyProfit($amount);
                $totalReturn = $machine->getTotalReturn($amount);
                $startDate = now();
                $endDate = $startDate->copy()->addDays($machine->duration_days);

                // Create investment record
                MachineInvestment::create([
                    'user_id' => $user->id,
                    'machine_id' => $machine->id,
                    'vip_level' => $vipLevel,
                    'amount' => $amount,
                    'daily_profit' => $dailyProfit,
                    'total_return' => $totalReturn,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => MachineInvestment::STATUS_ACTIVE,
                    'profit_credited' => 0,
                ]);
            });

            // Clear caches
            Cache::forget('machines_active');
            Cache::forget('dashboard_' . $user->id);

            return response()->json([
                'success' => true,
                'message' => 'Investment successful! Your daily profit will start accruing tomorrow.',
                'data' => [
                    'amount' => $amount,
                    'daily_profit' => $dailyProfit,
                    'total_return' => $totalReturn,
                    'end_date' => $endDate->format('Y-m-d'),
                ],
                'redirect' => route('machines.show', $machine->code)
            ]);
        } catch (\Exception $e) {
            Log::error('Investment failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Investment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function status(MachineInvestment $investment)
    {
        if ($investment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $investment->id,
                'amount' => $investment->amount,
                'daily_profit' => $investment->daily_profit,
                'current_profit' => $investment->currentProfit(),
                'total_return' => $investment->total_return,
                'profit_credited' => $investment->profit_credited,
                'start_date' => $investment->start_date->format('Y-m-d'),
                'end_date' => $investment->end_date->format('Y-m-d'),
                'days_elapsed' => $investment->daysElapsed(),
                'days_remaining' => $investment->daysRemaining(),
                'progress_percentage' => $investment->progressPercentage(),
                'status' => $investment->status,
            ]
        ]);
    }

    public function myInvestments()
    {
        $user = Auth::user();
        
        $investments = MachineInvestment::where('user_id', $user->id)
            ->with('machine')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($inv) {
                return [
                    'id' => $inv->id,
                    'machine_name' => $inv->machine->name,
                    'machine_code' => $inv->machine->code,
                    'vip_level' => $inv->vip_level,
                    'amount' => $inv->amount,
                    'daily_profit' => $inv->daily_profit,
                    'total_return' => $inv->total_return,
                    'profit_credited' => $inv->profit_credited,
                    'status' => $inv->status,
                    'start_date' => $inv->start_date->format('Y-m-d'),
                    'end_date' => $inv->end_date->format('Y-m-d'),
                    'progress' => $inv->progressPercentage(),
                    'days_remaining' => $inv->daysRemaining(),
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $investments
        ]);
    }
}
