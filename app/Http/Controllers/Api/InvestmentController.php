<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\Machine;
use App\Services\Investment\UnifiedInvestmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestmentController extends Controller
{
    protected $unifiedService;

    public function __construct(UnifiedInvestmentService $unifiedService)
    {
        $this->unifiedService = $unifiedService;
    }

    /**
     * Get all investments (legacy + RX machines)
     */
    public function index()
    {
        $user = Auth::user();
        $investments = $this->unifiedService->getAllInvestments($user);
        
        return response()->json([
            'success' => true,
            'data' => $investments,
            'meta' => [
                'total_invested' => $this->unifiedService->getTotalInvested($user),
                'total_profit' => $this->unifiedService->getTotalProfit($user),
            ]
        ]);
    }

    /**
     * Get available investment options (RX Machines)
     */
    public function plans()
    {
        $machines = Machine::where('is_active', true)->get()->map(function ($machine) {
            return [
                'id' => $machine->id,
                'code' => $machine->code,
                'name' => $machine->name,
                'type' => 'rx_machine',
                'duration_days' => $machine->duration_days,
                'growth_rate' => $machine->growth_rate,
                'vip_tiers' => $machine->getVIPDetails(),
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $machines,
            'message' => 'Use /api/v1/machines to invest in RX Machine Series'
        ]);
    }

    /**
     * Create investment (redirect to machines API)
     */
    public function store(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Please use POST /api/v1/machines/{machine}/invest for new investments',
            'redirect' => '/api/v1/machines'
        ], 422);
    }

    /**
     * Get investment details
     */
    public function show($id)
    {
        $investment = Auth::user()->investments()->with('plan', 'machine')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $investment->id,
                'type' => $investment->machine_id ? 'rx_machine' : 'legacy',
                'name' => $investment->machine?->name ?? $investment->plan?->name,
                'amount' => $investment->amount,
                'daily_profit' => $investment->daily_profit,
                'total_return' => $investment->total_projected_profit,
                'status' => $investment->status,
                'start_date' => $investment->start_date,
                'end_date' => $investment->end_date,
                'progress' => $investment->progressPercentage(),
                'days_remaining' => $investment->daysRemaining(),
            ]
        ]);
    }

    /**
     * Get investment statistics
     */
    public function stats()
    {
        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'total_invested' => $this->unifiedService->getTotalInvested($user),
                'total_profit' => $this->unifiedService->getTotalProfit($user),
                'active_investments' => $user->investments()->active()->count() + $user->machineInvestments()->active()->count(),
                'completed_investments' => $user->investments()->where('status', 'completed')->count() + $user->machineInvestments()->where('status', 'completed')->count(),
            ]
        ]);
    }
}
