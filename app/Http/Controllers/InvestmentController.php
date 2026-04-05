<?php

namespace App\Http\Controllers;

use App\Models\InvestmentPlan;
use App\Services\Investment\InvestmentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class InvestmentController extends Controller
{
    protected $investmentManager;

    public function __construct(InvestmentManager $investmentManager)
    {
        $this->investmentManager = $investmentManager;
    }

    public function index()
    {
        $plans = Cache::remember('active_investment_plans', 600, function () {
            return InvestmentPlan::where('is_active', true)->orderBy('min_amount')->get();
        });

        $user = Auth::user();
        $investments = $user->investments()->with('plan')->latest()->get();

        // Calculate totals for the summary cards
        $totalInvested = $investments->sum('amount');
        $totalProjected = $investments->sum('total_projected_profit');

        // For each plan, compute VIP amounts using golden ratio
        $plansWithVIP = $plans->map(function ($plan) {
            $vipAmounts = $plan->getVIPAmounts();
            $plan->vip_amounts = $vipAmounts;
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
        ]);

        $plan = InvestmentPlan::findOrFail($request->plan_id);
        $vipAmounts = $plan->getVIPAmounts();
        $amount = $vipAmounts[$request->vip_level];

        if (abs($request->amount - $amount) > 0.01) {
            return back()->withErrors(['error' => 'The amount must match the selected VIP level.']);
        }

        try {
            $this->investmentManager->create(auth()->user(), $plan, $amount);
            return redirect()->route('web.investments')->with('success', 'Investment created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
