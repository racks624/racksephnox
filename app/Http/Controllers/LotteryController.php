<?php

namespace App\Http\Controllers;

use App\Models\LotteryGame;
use App\Models\LotterySpin;
use App\Models\LotteryTournament;
use App\Services\LotteryService;
use App\Services\LotteryMissionService;
use App\Services\LotteryBonusWheelService;
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
        $activeTournament = LotteryTournament::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
        $missionService = new LotteryMissionService();
        $missions = $missionService->getTodayMissions($user);
        $completedMissions = collect($missions)->where('completed', true)->count();
        $totalMissions = count($missions);
        $bonusWheelService = new LotteryBonusWheelService();
        $canSpinBonusWheel = $bonusWheelService->canSpin($user);
        return view('lottery.index', compact(
            'game', 'balance', 'history', 'canFreeSpin', 'freeSpinHours', 'leaderboard',
            'activeTournament', 'completedMissions', 'totalMissions', 'canSpinBonusWheel'
        ));
    }

    public function spin(Request $request)
    {
        $request->validate(['bet' => 'required|numeric|min:1', 'client_seed' => 'nullable|string']);
        $game = LotteryGame::where('is_active', true)->firstOrFail();
        $service = new LotteryService($game);
        try {
            $result = $service->play(Auth::user(), $request->bet, false, $request->client_seed);
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
                'nonce' => $result['nonce'],
                'client_seed' => $result['client_seed'],
                'server_seed_hashed' => $result['server_seed_hashed'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function freeSpin(Request $request)
    {
        $game = LotteryGame::where('is_active', true)->firstOrFail();
        $service = new LotteryService($game);
        if (!$service->canUseFreeSpin(Auth::user())) {
            return response()->json(['success' => false, 'message' => 'Free spin already used today.'], 422);
        }
        try {
            $result = $service->play(Auth::user(), 0, true, $request->client_seed ?? null);
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
                'nonce' => $result['nonce'],
                'client_seed' => $result['client_seed'],
                'server_seed_hashed' => $result['server_seed_hashed'],
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

    public function verifySpin(Request $request)
    {
        $request->validate(['spin_id' => 'required|exists:lottery_spins,id', 'server_seed' => 'required|string']);
        $spin = LotterySpin::findOrFail($request->spin_id);
        if ($spin->user_id !== Auth::id() && !Auth::user()->is_admin) abort(403);
        $game = LotteryGame::where('is_active', true)->firstOrFail();
        $service = new LotteryService($game);
        $result = $service->verifySpin($spin, $request->server_seed);
        return response()->json($result);
    }

    public function prediction()
    {
        $user = Auth::user();
        $service = new \App\Services\LotteryPredictionService();
        $prediction = $service->analyze($user);
        return response()->json($prediction);
    }
}
