<?php

namespace App\Http\Controllers;

use App\Models\InvestmentPlan;
use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class InvestmentController extends Controller
{
    /**
     * Show investment options (redirect to RX Machines)
     */
    public function index()
    {
        $machines = Cache::remember('machines_for_investment', 300, function () {
            return Machine::where('is_active', true)->get();
        });
        
        $user = Auth::user();
        $activeMachineInvestments = $user->machineInvestments()
            ->where('status', 'active')
            ->with('machine')
            ->get();
        
        $legacyInvestments = $user->investments()
            ->with('plan')
            ->whereNull('machine_id')
            ->where('status', 'active')
            ->get();
        
        return view('investments.index', compact('machines', 'activeMachineInvestments', 'legacyInvestments'));
    }

    /**
     * Store investment (redirect to machines)
     */
    public function store(Request $request)
    {
        return redirect()->route('machines.index')->with('info', '✨ Please use the RX Machine Series for new investments with VIP tiers and 8888 Hz Wealth Frequency.');
    }

    /**
     * Show investment details
     */
    public function show($id)
    {
        $investment = Auth::user()->investments()->with('plan')->findOrFail($id);
        return view('investments.show', compact('investment'));
    }
}
