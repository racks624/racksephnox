<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\InvestmentPlan;
use App\Services\Investment\InvestmentService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    use ApiResponse;

    protected $investmentService;

    public function __construct(InvestmentService $investmentService)
    {
        $this->investmentService = $investmentService;
    }

    public function plans()
    {
        $plans = InvestmentPlan::where('is_active', true)->get();
        // Add VIP amounts
        $plans->map(function ($plan) {
            $phi = 1.61803398875;
            $vip1 = $plan->min_amount;
            $plan->vip_amounts = [
                1 => $vip1,
                2 => round($vip1 * $phi, 2),
                3 => round($vip1 * pow($phi, 2), 2),
            ];
            return $plan;
        });
        return $this->successResponse($plans);
    }

    public function invest(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:investment_plans,id',
            'amount' => 'required|numeric|min:0',
            'auto_reinvest' => 'sometimes|boolean',
            'compound_type' => 'sometimes|in:daily_payout,reinvest',
        ]);

        $plan = InvestmentPlan::findOrFail($request->plan_id);
        
        try {
            $investment = $this->investmentService->create(
                $request->user(),
                $plan,
                $request->amount,
                $request->boolean('auto_reinvest', false),
                $request->get('compound_type', 'daily_payout')
            );
            return $this->successResponse($investment, 'Investment created successfully.', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function myInvestments(Request $request)
    {
        $investments = $request->user()->investments()->with('plan')->latest()->get();
        return $this->successResponse($investments);
    }
}
