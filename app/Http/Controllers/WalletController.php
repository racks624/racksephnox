<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\LotterySpin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $wallet = $user->wallet;
        $currency = $user->preferred_currency ?? 'KES';
        $balance = $wallet->getBalanceInCurrency($currency);

        $totalWagered = LotterySpin::where('user_id', $user->id)->sum('bet_amount');
        $totalWon = LotterySpin::where('user_id', $user->id)->sum('win_amount');
        $miniJackpotHits = LotterySpin::where('user_id', $user->id)->where('mini_jackpot_hit', true)->count();
        $superJackpotHits = LotterySpin::where('user_id', $user->id)->where('super_jackpot_hit', true)->count();

        return view('wallet.index', compact('wallet', 'balance', 'currency', 'totalWagered', 'totalWon', 'miniJackpotHits', 'superJackpotHits'));
    }

    public function setCurrency(Request $request)
    {
        $request->validate(['currency' => 'required|in:KES,USD,EUR,BTC,ETH,USDT']);
        $user = Auth::user();
        $user->preferred_currency = $request->currency;
        $user->save();
        return response()->json(['success' => true]);
    }
}
