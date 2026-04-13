<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    /**
     * Get referral statistics
     */
    public function stats()
    {
        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'total_referrals' => $user->referrals()->count(),
                'total_bonus' => $user->transactions()->where('type', 'referral_bonus')->sum('amount'),
                'referral_link' => url('/refer/' . $user->referral_code),
            ]
        ]);
    }

    /**
     * Get list of referred users
     */
    public function list()
    {
        $user = Auth::user();
        
        $referrals = $user->referrals()->get()->map(function ($referral) {
            return [
                'id' => $referral->id,
                'name' => $referral->name,
                'email' => $referral->email,
                'joined' => $referral->created_at->format('Y-m-d'),
                'status' => $referral->is_verified ? 'verified' : 'pending',
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $referrals,
            'total' => $referrals->count(),
        ]);
    }

    /**
     * Get referral bonuses history
     */
    public function bonuses()
    {
        $user = Auth::user();
        
        $bonuses = $user->transactions()
            ->where('type', 'referral_bonus')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($bonus) {
                return [
                    'id' => $bonus->id,
                    'amount' => $bonus->amount,
                    'description' => $bonus->description,
                    'date' => $bonus->created_at->format('Y-m-d H:i:s'),
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $bonuses,
            'total_bonus' => $bonuses->sum('amount'),
        ]);
    }

    /**
     * Get referral link
     */
    public function getLink()
    {
        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'referral_link' => url('/refer/' . $user->referral_code),
                'referral_code' => $user->referral_code,
            ]
        ]);
    }
}
