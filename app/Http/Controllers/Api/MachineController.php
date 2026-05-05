<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $machines = Cache::remember('api_machines_all', 300, function () {
            return Machine::where('is_active', true)->get()->map(function ($machine) {
                return $this->formatMachineData($machine);
            });
        });
        return response()->json(['success' => true, 'data' => $machines]);
    }

    public function publicList()
    {
        $machines = Machine::where('is_active', true)->get()->map(function ($machine) {
            return [
                'code' => $machine->code,
                'name' => $machine->name,
                'risk_profile' => $machine->risk_profile,
                'duration_days' => $machine->duration_days,
                'growth_rate' => $machine->growth_rate,
                'vip1_amount' => $machine->getVIPAmounts()[1],
                'daily_profit_vip1' => $machine->getDailyProfit($machine->getVIPAmounts()[1]),
                'total_return_vip1' => $machine->getTotalReturn($machine->getVIPAmounts()[1]),
            ];
        });
        return response()->json(['success' => true, 'data' => $machines]);
    }

    public function show($code)
    {
        $machine = Machine::where('code', $code)->where('is_active', true)->firstOrFail();
        return response()->json(['success' => true, 'data' => $this->formatMachineData($machine, true)]);
    }

    public function invest(Request $request, Machine $machine)
    {
        $request->validate(['vip_level' => 'required|in:1,2,3']);
        $vipLevel = (int) $request->vip_level;
        $amount = $machine->getStartAmountForVip($vipLevel);
        if (!$amount) return response()->json(['success' => false, 'message' => 'Invalid VIP level'], 422);

        $user = Auth::user();
        $existing = MachineInvestment::where('user_id', $user->id)->where('machine_id', $machine->id)->where('status', 'active')->first();
        if ($existing) return response()->json(['success' => false, 'message' => 'Already active investment'], 422);
        if ($user->wallet->balance < $amount) return response()->json(['success' => false, 'message' => 'Insufficient balance'], 422);

        try {
            DB::transaction(function () use ($user, $machine, $amount, $vipLevel) {
                $user->wallet->decrement('balance', $amount);
                $user->transactions()->create(['type' => 'machine_investment', 'amount' => -$amount, 'status' => 'completed',
                    'description' => "Investment in {$machine->name} - VIP {$vipLevel}", 'balance_after' => $user->wallet->balance]);

                $dailyProfit = $machine->getDailyProfit($amount, $vipLevel);
                $totalReturn = $machine->getTotalReturn($amount, $vipLevel);
                MachineInvestment::create(['user_id' => $user->id, 'machine_id' => $machine->id, 'vip_level' => $vipLevel,
                    'amount' => $amount, 'daily_profit' => $dailyProfit, 'total_return' => $totalReturn,
                    'start_date' => now(), 'end_date' => now()->addDays($machine->duration_days),
                    'status' => 'active', 'profit_credited' => 0]);
            });
            Cache::forget('api_machines_all');
            return response()->json(['success' => true, 'message' => 'Investment successful!']);
        } catch (\Exception $e) {
            Log::error('Investment failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Investment failed'], 500);
        }
    }

    private function formatMachineData(Machine $machine, $includeDetails = false)
    {
        $data = [
            'code' => $machine->code, 'name' => $machine->name, 'risk_profile' => $machine->risk_profile,
            'duration_days' => $machine->duration_days, 'growth_rate' => $machine->growth_rate,
            'vip_tiers' => array_values($machine->getVIPDetails()), 'statistics' => $machine->getStatistics(),
        ];
        if ($includeDetails && Auth::user()) {
            $data['user_investment'] = MachineInvestment::where('user_id', Auth::id())->where('machine_id', $machine->id)
                ->where('status', 'active')->first();
        }
        return $data;
    }
}
