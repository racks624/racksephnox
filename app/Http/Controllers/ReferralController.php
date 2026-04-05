<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReferralController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Cache referral data for 5 minutes
        $referralData = Cache::remember('referral_data_' . $user->id, 300, function () use ($user) {
            $referrals = $user->referrals()->with('wallet')->get();
            $totalReferrals = $referrals->count();
            $totalBonus = $user->transactions()->where('type', 'referral_bonus')->sum('amount');
            
            // Monthly stats for chart
            $monthlyStats = $user->referrals()
                ->selectRaw('strftime("%Y-%m", created_at) as month, count(*) as count')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get();
            
            return compact('referrals', 'totalReferrals', 'totalBonus', 'monthlyStats');
        });
        
        return view('referrals', $referralData);
    }
    
    public function show($code)
    {
        $user = User::where('referral_code', $code)->firstOrFail();
        session(['ref' => $user->id]);
        return redirect()->route('register');
    }
    
    /**
     * API endpoint for real‑time referral stats (used by dashboard widget)
     */
    public function apiStats()
    {
        $user = auth()->user();
        $stats = Cache::remember('referral_stats_api_' . $user->id, 60, function () use ($user) {
            return [
                'total_referrals' => $user->referrals()->count(),
                'total_bonus' => $user->transactions()->where('type', 'referral_bonus')->sum('amount'),
                'active_referrals' => $user->referrals()
                    ->whereHas('transactions', function ($q) {
                        $q->where('type', 'deposit')->where('amount', '>', 0);
                    })->count(),
            ];
        });
        return response()->json($stats);
    }
}
