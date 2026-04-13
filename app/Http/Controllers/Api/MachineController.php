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
    /**
     * Get all active machines (authenticated)
     */
    public function index()
    {
        $machines = Cache::remember('api_machines_all', 300, function () {
            return Machine::where('is_active', true)->get()->map(function ($machine) {
                return $this->formatMachineData($machine);
            });
        });
        
        return response()->json([
            'success' => true,
            'data' => $machines,
            'meta' => [
                'total' => $machines->count(),
                'version' => 'v1',
                'timestamp' => now(),
            ]
        ]);
    }

    /**
     * Get public machine list (no auth required)
     */
    public function publicList()
    {
        $machines = Machine::where('is_active', true)->get()->map(function ($machine) {
            return [
                'code' => $machine->code,
                'name' => $machine->name,
                'description' => $machine->description,
                'risk_profile' => $machine->risk_profile,
                'duration_days' => $machine->duration_days,
                'growth_rate' => $machine->growth_rate,
                'icon' => $machine->icon,
                'vip1_amount' => $machine->getVIPAmounts()[1],
                'daily_profit_vip1' => $machine->getDailyProfit($machine->getVIPAmounts()[1]),
                'total_return_vip1' => $machine->getTotalReturn($machine->getVIPAmounts()[1]),
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $machines,
            'message' => 'Public machine listing'
        ]);
    }

    /**
     * Get single machine details
     */
    public function show($code)
    {
        $machine = Machine::where('code', $code)->where('is_active', true)->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => $this->formatMachineData($machine, true),
        ]);
    }

    /**
     * Get public machine details (no auth)
     */
    public function publicShow($code)
    {
        $machine = Machine::where('code', $code)->where('is_active', true)->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => [
                'code' => $machine->code,
                'name' => $machine->name,
                'description' => $machine->description,
                'risk_profile' => $machine->risk_profile,
                'duration_days' => $machine->duration_days,
                'growth_rate' => $machine->growth_rate,
                'vip_tiers' => array_values($machine->getVIPDetails()),
                'statistics' => $machine->getStatistics(),
            ]
        ]);
    }

    /**
     * Invest in a machine
     */
    public function invest(Request $request, Machine $machine)
    {
        $request->validate([
            'vip_level' => 'required|in:1,2,3,4,5,6',
        ]);

        $vipLevel = (int) $request->vip_level;
        $amount = $machine->getVIPAmounts()[$vipLevel] ?? null;
        
        if (!$amount) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid VIP level selected'
            ], 422);
        }
        
        $user = Auth::user();

        // Check existing active investment
        $existing = MachineInvestment::where('user_id', $user->id)
            ->where('machine_id', $machine->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active investment in this machine'
            ], 422);
        }

        // Check wallet balance
        if ($user->wallet->balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient wallet balance. Required: KES ' . number_format($amount, 2)
            ], 422);
        }

        // Check machine capacity
        if (!$machine->canAcceptInvestment($amount)) {
            return response()->json([
                'success' => false,
                'message' => 'This machine has reached its investment limit'
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
                $dailyProfit = $machine->getDailyProfit($amount, $vipLevel);
                $totalReturn = $machine->getTotalReturn($amount, $vipLevel);
                
                // Create investment record
                $investment = MachineInvestment::create([
                    'user_id' => $user->id,
                    'machine_id' => $machine->id,
                    'vip_level' => $vipLevel,
                    'amount' => $amount,
                    'daily_profit' => $dailyProfit,
                    'total_return' => $totalReturn,
                    'start_date' => now(),
                    'end_date' => now()->addDays($machine->duration_days),
                    'status' => 'active',
                    'profit_credited' => 0,
                ]);
            });

            // Clear caches
            Cache::forget('api_machines_all');
            Cache::forget("api_machine_{$machine->code}");
            Cache::forget('dashboard_' . $user->id);

            return response()->json([
                'success' => true,
                'message' => 'Investment successful! Daily profits will start accruing tomorrow.',
                'data' => [
                    'investment_id' => $investment->id ?? null,
                    'amount' => $amount,
                    'daily_profit' => $dailyProfit,
                    'total_return' => $totalReturn,
                    'end_date' => now()->addDays($machine->duration_days)->format('Y-m-d'),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Machine investment failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Investment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's all machine investments
     */
    public function myInvestments(Request $request)
    {
        $user = Auth::user();
        
        $query = MachineInvestment::where('user_id', $user->id)->with('machine');
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $investments = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $investments->map(function ($inv) {
                return [
                    'id' => $inv->id,
                    'machine_name' => $inv->machine->name,
                    'machine_code' => $inv->machine->code,
                    'vip_level' => $inv->vip_level,
                    'amount' => $inv->amount,
                    'daily_profit' => $inv->daily_profit,
                    'total_return' => $inv->total_return,
                    'profit_credited' => $inv->profit_credited,
                    'current_profit' => $inv->currentProfit(),
                    'progress' => $inv->progressPercentage(),
                    'days_elapsed' => $inv->daysElapsed(),
                    'days_remaining' => $inv->daysRemaining(),
                    'start_date' => $inv->start_date->format('Y-m-d'),
                    'end_date' => $inv->end_date->format('Y-m-d'),
                    'status' => $inv->status,
                ];
            }),
            'meta' => [
                'total_invested' => $investments->sum('amount'),
                'total_profit_earned' => $investments->sum('profit_credited'),
                'total_projected' => $investments->sum('total_return'),
            ]
        ]);
    }

    /**
     * Get investment status
     */
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

    /**
     * Get active investments only
     */
    public function activeInvestments()
    {
        $user = Auth::user();
        
        $investments = MachineInvestment::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('machine')
            ->get()
            ->map(function ($inv) {
                return [
                    'id' => $inv->id,
                    'machine_name' => $inv->machine->name,
                    'vip_level' => $inv->vip_level,
                    'amount' => $inv->amount,
                    'daily_profit' => $inv->daily_profit,
                    'progress' => $inv->progressPercentage(),
                    'days_remaining' => $inv->daysRemaining(),
                    'end_date' => $inv->end_date->format('Y-m-d'),
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $investments,
            'total_active' => $investments->count(),
            'total_invested' => $investments->sum('amount'),
        ]);
    }

    /**
     * Get global machine statistics
     */
    public function globalStats()
    {
        $stats = Cache::remember('api_machines_global_stats', 300, function () {
            $machines = Machine::where('is_active', true)->get();
            $totalInvested = $machines->sum(function ($m) {
                return $m->investments()->sum('amount');
            });
            $totalPaidOut = $machines->sum(function ($m) {
                return $m->investments()->sum('total_return');
            });
            $activeInvestments = $machines->sum(function ($m) {
                return $m->activeInvestments()->count();
            });
            $totalInvestors = MachineInvestment::distinct('user_id')->count('user_id');
            
            return [
                'total_machines' => $machines->count(),
                'total_invested' => $totalInvested,
                'total_paid_out' => $totalPaidOut,
                'total_profit' => $totalPaidOut - $totalInvested,
                'active_investments' => $activeInvestments,
                'total_investors' => $totalInvestors,
                'avg_roi' => $machines->avg('growth_rate'),
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Format machine data for API response
     */
    private function formatMachineData(Machine $machine, $includeDetails = false)
    {
        $user = Auth::user();
        
        $data = [
            'id' => $machine->id,
            'code' => $machine->code,
            'name' => $machine->name,
            'description' => $machine->description,
            'risk_profile' => $machine->risk_profile,
            'duration_days' => $machine->duration_days,
            'growth_rate' => $machine->growth_rate,
            'icon' => $machine->icon,
            'color' => $machine->color,
            'vip_tiers' => array_values($machine->getVIPDetails()),
            'statistics' => $machine->getStatistics(),
        ];
        
        if ($includeDetails && $user) {
            $data['user_investment'] = MachineInvestment::where('user_id', $user->id)
                ->where('machine_id', $machine->id)
                ->where('status', 'active')
                ->first();
        }
        
        return $data;
    }
}
