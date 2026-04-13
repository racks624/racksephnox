<?php

namespace App\Http\Controllers;

use App\Models\LotteryGame;
use App\Models\LotterySpin;
use App\Services\LotteryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LotteryController extends Controller
{
    public function index()
    {
        $game = LotteryGame::where('is_active', true)->firstOrFail();
        $user = Auth::user();
        $balance = $user->wallet->balance ?? 0;
        $service = new LotteryService($game);
        $canFreeSpin = $service->canUseFreeSpin($user);
        $freeSpinHours = $service->getNextFreeSpinHours($user);
        $history = LotterySpin::where('user_id', $user->id)->latest()->take(10)->get();
        $leaderboard = LotterySpin::where('created_at', '>=', now()->startOfWeek())
            ->selectRaw('user_id, SUM(win_amount) as total_win')
            ->groupBy('user_id')
            ->orderBy('total_win', 'desc')
            ->with('user')
            ->take(5)
            ->get();
        return view('lottery.index', compact('game', 'balance', 'history', 'canFreeSpin', 'freeSpinHours', 'leaderboard'));
    }

    public function spin(Request $request)
    {
        $request->validate(['bet' => 'required|numeric|min:1']);
        $game = LotteryGame::where('is_active', true)->firstOrFail();
        $service = new LotteryService($game);
        try {
            $result = $service->play(Auth::user(), $request->bet);
            return response()->json([
                'success' => true,
                'symbols' => array_map(fn($s) => ['name' => $s->name, 'display_name' => $s->display_name, 'icon' => $s->icon], $result['symbols']),
                'win_amount' => $result['win_amount'],
                'net_change' => $result['net_change'],
                'mini_jackpot' => $result['mini_jackpot'],
                'super_jackpot' => $result['super_jackpot'],
                'free_spin_trigger' => $result['free_spin_trigger'],
                'progressive_jackpot' => $result['progressive_jackpot'],
                'new_balance' => Auth::user()->wallet->fresh()->balance,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function freeSpin()
    {
        $game = LotteryGame::where('is_active', true)->firstOrFail();
        $service = new LotteryService($game);
        if (!$service->canUseFreeSpin(Auth::user())) {
            return response()->json(['success' => false, 'message' => 'Free spin already used today.'], 422);
        }
        try {
            $result = $service->play(Auth::user(), 0, true);
            return response()->json([
                'success' => true,
                'symbols' => array_map(fn($s) => ['name' => $s->name, 'display_name' => $s->display_name, 'icon' => $s->icon], $result['symbols']),
                'win_amount' => $result['win_amount'],
                'net_change' => $result['net_change'],
                'mini_jackpot' => $result['mini_jackpot'],
                'super_jackpot' => $result['super_jackpot'],
                'free_spin_trigger' => $result['free_spin_trigger'],
                'progressive_jackpot' => $result['progressive_jackpot'],
                'new_balance' => Auth::user()->wallet->fresh()->balance,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function history()
    {
        $history = LotterySpin::where('user_id', Auth::id())->latest()->paginate(20);
        return view('lottery.history', compact('history'));
    }

    public function leaderboard($period = 'weekly')
    {
        $startDate = match($period) {
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            default => now()->subDays(7),
        };
        $topWinners = LotterySpin::where('created_at', '>=', $startDate)
            ->selectRaw('user_id, SUM(win_amount) as total_win')
            ->groupBy('user_id')
            ->orderBy('total_win', 'desc')
            ->with('user')
            ->take(20)
            ->get();
        return view('lottery.leaderboard', compact('topWinners', 'period'));
    }

    public function jackpotStatus()
    {
        $game = LotteryGame::where('is_active', true)->first();
        return response()->json(['jackpot' => $game->progressive_jackpot ?? 1000]);
    }
}
