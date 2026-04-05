<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvestmentPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class InvestmentPlanController extends Controller
{
    public function index()
    {
        $plans = InvestmentPlan::latest()->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:investment_plans',
            'description' => 'nullable|string',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|gt:min_amount',
            'daily_interest_rate' => 'required|numeric|min:0|max:100',
            'duration_days' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        InvestmentPlan::create($request->all());

        // Clear cached active plans
        Cache::forget('active_investment_plans');

        return redirect()->route('admin.plans.index')->with('success', 'Plan created.');
    }

    public function edit(InvestmentPlan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, InvestmentPlan $plan)
    {
        $request->validate([
            'name' => 'required|string|unique:investment_plans,name,' . $plan->id,
            'description' => 'nullable|string',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|gt:min_amount',
            'daily_interest_rate' => 'required|numeric|min:0|max:100',
            'duration_days' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $plan->update($request->all());

        // Clear cached active plans
        Cache::forget('active_investment_plans');

        return redirect()->route('admin.plans.index')->with('success', 'Plan updated.');
    }

    public function destroy(InvestmentPlan $plan)
    {
        if ($plan->investments()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete plan with investments.']);
        }
        $plan->delete();
        Cache::forget('active_investment_plans');
        return redirect()->route('admin.plans.index')->with('success', 'Plan deleted.');
    }
}
