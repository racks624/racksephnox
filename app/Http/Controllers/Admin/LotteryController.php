<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LotteryGame;
use App\Models\LotterySymbol;
use App\Models\LotterySpin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LotteryController extends Controller
{
    public function index()
    {
        $games = LotteryGame::all();
        $symbols = LotterySymbol::all();
        $recentSpins = LotterySpin::with('user')->latest()->take(20)->get();
        $totalBets = LotterySpin::sum('bet_amount');
        $totalWins = LotterySpin::sum('win_amount');
        $miniJackpotHits = LotterySpin::where('mini_jackpot_hit', true)->count();
        $superJackpotHits = LotterySpin::where('super_jackpot_hit', true)->count();
        $totalTax = LotterySpin::sum('tax_contribution');
        return view('admin.lottery.index', compact('games', 'symbols', 'recentSpins', 'totalBets', 'totalWins', 'miniJackpotHits', 'superJackpotHits', 'totalTax'));
    }

    public function editGame(LotteryGame $game)
    {
        $symbols = LotterySymbol::all();
        return view('admin.lottery.edit', compact('game', 'symbols'));
    }

    public function updateGame(Request $request, LotteryGame $game)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'min_bet' => 'required|numeric|min:1',
            'max_bet' => 'required|numeric|min:1',
            'ticket_price' => 'required|numeric|min:1',
            'free_spins_award' => 'integer|min:0',
            'jackpot_contribution_rate' => 'numeric|min:0|max:100',
            'is_active' => 'boolean',
            'progressive_jackpot' => 'nullable|numeric',
            'base_rtp' => 'numeric|min:0|max:100',
            'vip_rtp' => 'numeric|min:0|max:100',
            'promo_rtp' => 'numeric|min:0|max:100',
            'volatility' => 'required|in:low,medium,high,extreme',
            'max_daily_loss' => 'nullable|numeric|min:0',
            'max_weekly_loss' => 'nullable|numeric|min:0',
            'max_monthly_loss' => 'nullable|numeric|min:0',
            'max_win_cap' => 'nullable|numeric|min:0',
            'cool_down_minutes' => 'nullable|integer|min:0',
            'session_timeout_minutes' => 'nullable|integer|min:0',
            'enable_free_spins' => 'boolean',
            'enable_bonus_buy' => 'boolean',
            'bonus_buy_price' => 'nullable|numeric|min:0',
        ]);
        $game->update($validated);
        Cache::forget('lottery_symbols');
        return redirect()->route('admin.lottery.index')->with('success', 'Game updated.');
    }

    public function editSymbol(LotterySymbol $symbol) { /* ... */ }
    public function updateSymbol(Request $request, LotterySymbol $symbol) { /* ... */ }
    public function stats() { /* ... */ }
}
