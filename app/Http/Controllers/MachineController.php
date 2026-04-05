<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\MachineInvestment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
            ->get();

        $totalInvested = $activeInvestments->sum('amount');
        $totalProjectedProfit = $activeInvestments->sum('total_return') - $totalInvested;

        return view('machines.index', compact('machines', 'activeInvestments', 'totalInvested', 'totalProjectedProfit'));
    }

    public function show($code)
    {
        $machine = Machine::where('code', $code)->where('is_active', true)->firstOrFail();
        $user = Auth::user();

        $activeInvestment = $user->machineInvestments()
            ->where('machine_id', $machine->id)
            ->where('status', MachineInvestment::STATUS_ACTIVE)
            ->first();

        $investmentHistory = $user->machineInvestments()
            ->where('machine_id', $machine->id)
            ->where('status', MachineInvestment::STATUS_COMPLETED)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $vipAmounts = $machine->getVIPAmounts();

        return view('machines.show', compact('machine', 'activeInvestment', 'investmentHistory', 'vipAmounts'));
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
            return response()->json(['error' => 'You already have an active investment in this machine.'], 422);
        }

        // Check wallet balance
        if ($user->wallet->balance < $amount) {
            return response()->json(['error' => 'Insufficient wallet balance. Please deposit funds.'], 422);
        }

        try {
            DB::transaction(function () use ($user, $machine, $amount, $vipLevel) {
                // Debit wallet
                $user->wallet->debit($amount, 'Machine investment: ' . $machine->name . ' VIP ' . $vipLevel);

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
                ]);
            });

            // Clear caches
            Cache::forget('machines_active');
            Cache::forget('dashboard_' . $user->id);

            return response()->json([
                'success' => true,
                'message' => 'Investment successful! Your daily profit will start accruing tomorrow.',
                'redirect' => route('machines.show', $machine->code)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Investment failed: ' . $e->getMessage()], 500);
        }
    }
}
