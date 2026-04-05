<?php

namespace App\Http\Controllers;

use App\Models\InvestmentPlan;
use App\Models\Investment;
use App\Services\Investment\InvestmentService;
use App\Notifications\InvestmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class InvestmentWebController extends Controller
{
    protected $investmentService;

    public function __construct(InvestmentService $investmentService)
    {
        $this->investmentService = $investmentService;
    }

    public function index()
    {
        $plans = Cache::remember('active_investment_plans', 600, function () {
            return InvestmentPlan::where('is_active', true)->orderBy('min_amount')->get();
        });

        $user = Auth::user();
        $investments = $user->investments()->with('plan')->latest()->get();

        $totalInvested = $investments->where('status', Investment::STATUS_ACTIVE)->sum('amount');
        $totalProjected = $investments->where('status', Investment::STATUS_ACTIVE)->sum('total_projected_profit');

        $plansWithVIP = $plans->map(function ($plan) {
            $phi = 1.61803398875;
            $vip1 = $plan->min_amount;
            $plan->vip_amounts = [
                1 => $vip1,
                2 => round($vip1 * $phi, 2),
                3 => round($vip1 * pow($phi, 2), 2),
            ];
            return $plan;
        });

        return view('investments', compact('plansWithVIP', 'investments', 'totalInvested', 'totalProjected'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:investment_plans,id',
            'vip_level' => 'required|in:1,2,3',
            'amount' => 'required|numeric|min:0',
            'auto_reinvest' => 'sometimes|boolean',
            'compound_type' => 'sometimes|in:daily_payout,reinvest',
        ]);

        $plan = InvestmentPlan::findOrFail($request->plan_id);
        $phi = 1.61803398875;
        $vipAmounts = [
            1 => $plan->min_amount,
            2 => round($plan->min_amount * $phi, 2),
            3 => round($plan->min_amount * pow($phi, 2), 2),
        ];
        $amount = $vipAmounts[$request->vip_level];

        if (abs($request->amount - $amount) > 0.01) {
            return back()->withErrors(['error' => 'The amount must match the selected VIP level.']);
        }

        try {
            $investment = $this->investmentService->create(
                auth()->user(),
                $plan,
                $amount,
                $request->boolean('auto_reinvest', false),
                $request->get('compound_type', 'daily_payout')
            );
            
            auth()->user()->notify(new InvestmentNotification($plan->name, $amount));
            
            return redirect()->route('web.investments')->with('success', 'Investment created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function earlyWithdraw(Investment $investment)
    {
        if ($investment->user_id !== auth()->id()) {
            abort(403);
        }
        try {
            $refund = $investment->earlyWithdraw();
            return redirect()->route('web.investments')->with('success', "Early withdrawal processed. Refund: KES $refund");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
