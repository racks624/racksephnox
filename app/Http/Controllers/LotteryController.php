<?php

namespace App\Http\Controllers;

use App\Models\LotteryGame;
use App\Models\LotterySpin;
use App\Models\LotterySymbol;
use App\Models\LotteryTournament;
use App\Services\LotteryService;
use App\Services\LotteryMissionService;
use App\Services\LotteryBonusWheelService;
use App\Services\Lottery\AchievementService;
use App\Services\Lottery\StreakService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LotteryController extends Controller
{
    protected $game;
    protected $missionService;
    protected $bonusWheelService;
    protected $achievementService;
    protected $streakService;

    public function __construct()
    {
        $this->game = LotteryGame::where('is_active', true)->firstOrFail();
        $this->missionService = new LotteryMissionService();
        $this->bonusWheelService = new LotteryBonusWheelService();
        $this->achievementService = new AchievementService();
        $this->streakService = new StreakService();
    }

    /**
     * Main lottery page
     */
    public function index()
    {
        $user = Auth::user();
        $game = $this->game;  // ✅ FIX: define $game for compact()
        $balance = $user->wallet?->balance ?? 0;
        $service = new LotteryService($game);
        $canFreeSpin = $service->canUseFreeSpin($user);
        $freeSpinHours = $service->getNextFreeSpinHours($user);
        $history = LotterySpin::where('user_id', $user->id)->latest()->take(10)->get();

        // Get active symbols for the slot machine
        $symbols = LotterySymbol::where('is_active', true)->orderBy('sort_order')->get();

        // Weekly leaderboard
        $leaderboard = LotterySpin::where('created_at', '>=', now()->startOfWeek())
            ->selectRaw('user_id, SUM(win_amount) as total_win')
            ->groupBy('user_id')
            ->orderBy('total_win', 'desc')
            ->with('user')
            ->take(5)
            ->get();

        // Tournament data
        $activeTournament = LotteryTournament::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        // Missions
        $missions = $this->missionService->getTodayMissions($user);
        $completedMissions = collect($missions)->where('completed', true)->count();
        $totalMissions = count($missions);

        // Bonus wheel availability
        $canSpinBonusWheel = $this->bonusWheelService->canSpin($user);

        // Daily streak check
        $this->streakService->checkAndReward($user);

        return view('lottery.index', compact(
            'game', 'balance', 'history', 'canFreeSpin', 'freeSpinHours', 'leaderboard',
            'activeTournament', 'completedMissions', 'totalMissions', 'canSpinBonusWheel',
            'symbols'
        ));
    }

    /**
     * Regular spin
     */
    public function spin(Request $request)
    {
        $request->validate([
            'bet' => 'required|numeric|min:1',
            'client_seed' => 'nullable|string',
        ]);
        $service = new LotteryService($this->game);
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

    /**
     * Free daily spin
     */
    public function freeSpin(Request $request)
    {
        $service = new LotteryService($this->game);
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

    /**
     * Buy 10 free spins (Bonus Buy)
     */
    public function buyBonus(Request $request)
    {
        $request->validate(['cost' => 'required|numeric|min:100']);
        $user = Auth::user();
        if ($user->wallet->balance < $request->cost) {
            return response()->json(['success' => false, 'message' => 'Insufficient balance']);
        }
        DB::transaction(function () use ($user, $request) {
            $user->wallet->decrement('balance', $request->cost);
            $user->transactions()->create([
                'type' => 'bonus_buy',
                'amount' => -$request->cost,
                'status' => 'completed',
                'description' => 'Bonus Buy: 10 Free Spins',
                'balance_after' => $user->wallet->balance,
                'user_id' => $user->id,
                'wallet_id' => $user->wallet->id,
            ]);
            $user->free_spins_available = ($user->free_spins_available ?? 0) + 10;
            $user->save();
        });
        return response()->json(['success' => true, 'free_spins' => 10]);
    }

    /**
     * Gamble feature (double or nothing)
     */
    public function gamble(Request $request)
    {
        $request->validate([
            'spin_id' => 'required|exists:lottery_spins,id',
            'choice' => 'required|in:red,black',
        ]);
        $spin = LotterySpin::where('user_id', Auth::id())->findOrFail($request->spin_id);
        if ($spin->win_amount <= 0) {
            return response()->json(['success' => false, 'message' => 'No win to gamble']);
        }
        $result = rand(1, 2) == 1 ? 'red' : 'black';
        DB::transaction(function () use ($spin, $request, $result) {
            if ($request->choice === $result) {
                $newWin = $spin->win_amount * 2;
                $spin->user->wallet->increment('balance', $newWin - $spin->win_amount);
                $spin->win_amount = $newWin;
                $spin->save();
            } else {
                $spin->user->wallet->decrement('balance', $spin->win_amount);
                $spin->win_amount = 0;
                $spin->save();
            }
        });
        return response()->json(['success' => true, 'result' => $result, 'new_win' => $spin->win_amount]);
    }

    /**
     * Spin history
     */
    public function history()
    {
        $history = LotterySpin::where('user_id', Auth::id())->latest()->paginate(20);
        return view('lottery.history', compact('history'));
    }

    /**
     * Leaderboard (weekly, monthly, all-time)
     */
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

    /**
     * User achievements
     */
    public function achievements()
    {
        $user = Auth::user();
        $achievements = $user->achievements()->with('achievement')->get();
        $allAchievements = \App\Models\LotteryAchievement::all();
        return view('lottery.achievements', compact('achievements', 'allAchievements'));
    }

    /**
     * Personal lottery dashboard (stats)
     */
    public function dashboard()
    {
        $user = Auth::user();
        $stats = [
            'total_spins' => LotterySpin::where('user_id', $user->id)->count(),
            'total_bet' => LotterySpin::where('user_id', $user->id)->sum('bet_amount'),
            'total_win' => LotterySpin::where('user_id', $user->id)->sum('win_amount'),
            'mini_jackpots' => LotterySpin::where('user_id', $user->id)->where('mini_jackpot_hit', true)->count(),
            'super_jackpots' => LotterySpin::where('user_id', $user->id)->where('super_jackpot_hit', true)->count(),
            'free_spins' => $user->free_spins_available ?? 0,
        ];
        $stats['net_profit'] = $stats['total_win'] - $stats['total_bet'];
        $recentSpins = LotterySpin::where('user_id', $user->id)->latest()->take(10)->get();

        $activeTournament = LotteryTournament::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
        $tournamentRank = null;
        if ($activeTournament) {
            $entry = $activeTournament->entries()->where('user_id', $user->id)->first();
            $tournamentRank = $entry ? $entry->rank : null;
        }

        $missions = $this->missionService->getTodayMissions($user);
        $completedMissions = collect($missions)->where('completed', true)->count();
        $totalMissions = count($missions);

        return view('lottery.dashboard', compact('stats', 'recentSpins', 'tournamentRank', 'activeTournament', 'completedMissions', 'totalMissions'));
    }

    /**
     * Verify provably fair spin
     */
    public function verifySpin(Request $request)
    {
        $request->validate([
            'spin_id' => 'required|exists:lottery_spins,id',
            'server_seed' => 'required|string',
        ]);
        $spin = LotterySpin::findOrFail($request->spin_id);
        if ($spin->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }
        $service = new LotteryService($this->game);
        $result = $service->verifySpin($spin, $request->server_seed);
        return response()->json($result);
    }
}
