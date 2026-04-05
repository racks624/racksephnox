<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvestmentPlan;
use App\Models\Investment;
use App\Services\Investment\InvestmentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class InvestmentController extends Controller
{
    protected $investmentManager;

    public function __construct(InvestmentManager $investmentManager)
    {
        $this->investmentManager = $investmentManager;
    }

    /**
     * Get all active investment plans with VIP amounts.
     */
    public function plans()
    {
        $plans = Cache::remember('api_active_investment_plans', 600, function () {
            return InvestmentPlan::where('is_active', true)->orderBy('min_amount')->get();
        });

        // Add VIP amounts based on golden ratio
        $phi = 1.61803398875;
        $plans->map(function ($plan) use ($phi) {
            $vip1 = $plan->min_amount;
            $plan->vip_amounts = [
                1 => $vip1,
                2 => round($vip1 * $phi, 2),
                3 => round($vip1 * pow($phi, 2), 2),
            ];
            return $plan;
        });

        return response()->json([
            'status' => 'success',
            'data' => $plans,
        ]);
    }

    /**
     * Get user's investments.
     */
    public function index(Request $request)
    {
        $investments = $request->user()->investments()->with('plan')->latest()->get();
        return response()->json([
            'status' => 'success',
            'data' => $investments,
        ]);
    }

    /**
     * Create a new investment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:investment_plans,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $plan = InvestmentPlan::findOrFail($request->plan_id);

        try {
            $investment = $this->investmentManager->create($request->user(), $plan, $request->amount);
            return response()->json([
                'status' => 'success',
                'message' => 'Investment created successfully.',
                'data' => $investment,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Show a specific investment.
     */
    public function show($id)
    {
        $investment = auth()->user()->investments()->with('plan')->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $investment,
        ]);
    }

    /**
     * Calculate daily profit for a plan and amount (helper endpoint).
     */
    public function dailyProfit(InvestmentPlan $plan, Request $request)
    {
        $amount = $request->get('amount', 0);
        $dailyProfit = $plan->getDailyProfit($amount);
        return response()->json([
            'status' => 'success',
            'daily_profit' => $dailyProfit,
        ]);
    }
}
