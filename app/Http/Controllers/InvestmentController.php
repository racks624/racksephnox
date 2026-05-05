<?php
namespace App\Http\Controllers;

use App\Models\InvestmentPlan;
use App\Models\Machine;
use App\Services\Investment\UnifiedInvestmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class InvestmentController extends Controller
{
    protected $unifiedService;

    public function __construct(UnifiedInvestmentService $unifiedService)
    {
        $this->unifiedService = $unifiedService;
    }

    public function index()
    {
        $user = Auth::user();
        $investments = $this->unifiedService->getAllInvestments($user);
        $totalInvested = $this->unifiedService->getTotalInvested($user);
        $totalProfit = $this->unifiedService->getTotalProfit($user);

        return view('investments.unified', compact('investments', 'totalInvested', 'totalProfit'));
    }

    public function show($id)
    {
        $investment = Auth::user()->investments()->with('plan')->findOrFail($id);
        return view('investments.show', compact('investment'));
    }

    public function redirectToMachines()
    {
        return redirect()->route('machines.index')->with('info', '✨ Use RX Machines for new investments with VIP tiers.');
    }
}
