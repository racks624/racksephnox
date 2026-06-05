<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvestmentPlan;
use Illuminate\Http\Request;

class InvestmentPlanController extends Controller
{
    public function index()
    {
        $plans = InvestmentPlan::latest()->paginate(15);
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|gt:min_amount',
            'daily_interest_rate' => 'required|numeric|min:0|max:100',
            'duration_days' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'allow_auto_reinvest' => 'boolean',
            'allow_early_withdrawal' => 'boolean',
            'early_withdrawal_penalty' => 'numeric|min:0|max:100',
            'max_reinvestment_cycles' => 'integer|min:1',
            'is_infinite' => 'boolean',
        ]);

        InvestmentPlan::create($validated);
        return redirect()->route('admin.plans.index')->with('success', 'Investment plan created.');
    }

    public function edit(InvestmentPlan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, InvestmentPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|gt:min_amount',
            'daily_interest_rate' => 'required|numeric|min:0|max:100',
            'duration_days' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'allow_auto_reinvest' => 'boolean',
            'allow_early_withdrawal' => 'boolean',
            'early_withdrawal_penalty' => 'numeric|min:0|max:100',
            'max_reinvestment_cycles' => 'integer|min:1',
            'is_infinite' => 'boolean',
        ]);

        $plan->update($validated);
        return redirect()->route('admin.plans.index')->with('success', 'Plan updated.');
    }

    public function destroy(InvestmentPlan $plan)
    {
        $plan->delete();
        return back()->with('success', 'Plan deleted.');
    }

    public function export()
    {
        $plans = InvestmentPlan::all();
        $csv = "ID,Name,Min,Max,Rate,Days,Active\n";
        foreach ($plans as $plan) {
            $csv .= "{$plan->id},{$plan->name},{$plan->min_amount},{$plan->max_amount},{$plan->daily_interest_rate},{$plan->duration_days},{$plan->is_active}\n";
        }
        return response($csv, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="investment_plans.csv"']);
    }
}
