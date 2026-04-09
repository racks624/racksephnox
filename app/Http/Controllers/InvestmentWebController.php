<?php

namespace App\Http\Controllers;

use App\Services\Investment\UnifiedInvestmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestmentWebController extends Controller
{
    protected $unifiedService;

    public function __construct(UnifiedInvestmentService $unifiedService)
    {
        $this->unifiedService = $unifiedService;
    }

    /**
     * Show unified investments (redirects to machines by default)
     */
    public function index()
    {
        // If user has no investments, show machines
        $user = Auth::user();
        $hasInvestments = $this->unifiedService->getTotalInvested($user) > 0;
        
        if (!$hasInvestments) {
            return redirect()->route('machines.index')->with('info', '✨ Start your investment journey with our RX Machine Series featuring VIP tiers and Golden Ratio Φ returns.');
        }
        
        // Show unified view (optional: create a unified dashboard)
        $investments = $this->unifiedService->getAllInvestments($user);
        $totalInvested = $this->unifiedService->getTotalInvested($user);
        $totalProfit = $this->unifiedService->getTotalProfit($user);
        
        return view('investments.unified', compact('investments', 'totalInvested', 'totalProfit'));
    }

    /**
     * Store investment (redirect to machines)
     */
    public function store(Request $request)
    {
        return redirect()->route('machines.index')->with('info', '✨ Please select an RX Machine and VIP level to start investing.');
    }

    /**
     * Early withdrawal (redirect to machines)
     */
    public function earlyWithdraw($investment)
    {
        return redirect()->route('machines.index')->with('info', '✨ Please manage your active investments from the Machines section.');
    }
}
