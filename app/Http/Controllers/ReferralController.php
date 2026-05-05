<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $referrals = $user->referrals()->get();
        $bonuses = Transaction::where('user_id', $user->id)->where('type', 'referral_bonus')->latest()->get();
        $totalBonus = $bonuses->sum('amount');
        return view('referrals.index', compact('referrals', 'bonuses', 'totalBonus'));
    }
}
