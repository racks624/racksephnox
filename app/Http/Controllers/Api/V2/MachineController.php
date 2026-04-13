<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
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
        $machines = Cache::remember('api_v2_machines', 300, function () {
            return Machine::where('is_active', true)->get()->map(function ($machine) {
                $vipDetails = $machine->getVIPDetails();
                return [
                    'code' => $machine->code,
                    'name' => $machine->name,
                    'risk_profile' => $machine->risk_profile,
                    'duration_days' => $machine->duration_days,
                    'growth_rate' => $machine->growth_rate,
                    'vip_tiers' => array_map(function ($vip) {
                        return [
                            'level' => $vip['level'],
                            'amount' => $vip['amount'],
                            'daily_profit' => $vip['daily_profit'],
                            'total_return' => $vip['total_return'],
                            'wealth_tax_daily' => $vip['daily_tax'] ?? 0,
                        ];
                    }, array_values($vipDetails)),
                ];
            });
        });
        return response()->json(['success' => true, 'data' => $machines]);
    }

    public function show($code)
    {
        $machine = Machine::where('code', $code)->where('is_active', true)->firstOrFail();
        return response()->json(['success' => true, 'data' => $machine->getVIPDetails()]);
    }

    public function invest(Request $request, Machine $machine)
    {
        $request->validate(['vip_level' => 'required|in:1,2,3']);
        $vipLevel = (int) $request->vip_level;
        $amount = $machine->getStartAmountForVip($vipLevel);
        $user = Auth::user();

        // Check existing active investment
        if (MachineInvestment::where('user_id', $user->id)->where('machine_id', $machine->id)->where('status', 'active')->exists()) {
            return response()->json(['success' => false, 'message' => 'Already active investment in this machine.'], 422);
        }
        if ($user->wallet->balance < $amount) {
            return response()->json(['success' => false, 'message' => 'Insufficient balance.'], 422);
        }

        DB::transaction(function () use ($user, $machine, $amount, $vipLevel) {
            $user->wallet->decrement('balance', $amount);
            $user->transactions()->create([
                'type' => 'machine_investment',
                'amount' => -$amount,
                'status' => 'completed',
                'description' => "Investment in {$machine->name} VIP {$vipLevel}",
                'balance_after' => $user->wallet->balance,
            ]);
            MachineInvestment::create([
                'user_id' => $user->id,
                'machine_id' => $machine->id,
                'vip_level' => $vipLevel,
                'amount' => $amount,
                'daily_profit' => $machine->getDailyProfit($amount),
                'total_return' => $machine->getTotalReturn($amount),
                'start_date' => now(),
                'end_date' => now()->addDays($machine->duration_days),
                'status' => 'active',
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Investment successful.']);
    }
}
