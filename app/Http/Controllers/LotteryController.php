<?php
namespace App\Http\Controllers;
use App\Models\LotteryGame;
use App\Models\LotterySpin;
use App\Models\LotteryTournament;
use App\Services\LotteryService;
use App\Services\LotteryMissionService;
use App\Services\LotteryBonusWheelService;
use App\Services\Lottery\AchievementService;
use App\Services\Lottery\StreakService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

    protected function checkRateLimit(): void
    {
        $user = Auth::user();
        if (!$user) return;
        $key = 'lottery_spin_count_' . $user->id . '_' . now()->format('Y-m-d_H:i');
        $count = Cache::increment($key);
        if ($count > 60) {
            Cache::put($key, $count, 65);
            abort(429, 'Too many spins. Please wait a moment.');
        }
        Cache::put($key, $count, 65);
    }

    public function index()
    {
        $user = Auth::user();
        $game = $this->game;
        $balance = $user->wallet?->balance ?? 0;
        $service = new LotteryService($this->game);
        $canFreeSpin = $service->canUseFreeSpin($user);
        $freeSpinHours = $service->getNextFreeSpinHours($user);
        $history = LotterySpin::where('user_id', $user->id)->latest()->take(10)->get();
        $symbols = $game->symbols->map(fn($s) => ['name' => $s->name, 'icon' => $s->icon, 'display_name' => $s->display_name])->toArray();
        $leaderboard = LotterySpin::where('created_at', '>=', now()->startOfWeek())
            ->selectRaw('user_id, SUM(win_amount) as total_win')->groupBy('user_id')->orderBy('total_win', 'desc')->with('user')->take(5)->get();
        $activeTournament = LotteryTournament::where('is_active', true)->where('start_date', '<=', now())->where('end_date', '>=', now())->first();
        $missions = $this->missionService->getTodayMissions($user);
        $completedMissions = collect($missions)->where('completed', true)->count();
        $totalMissions = count($missions);
        $canSpinBonusWheel = $this->bonusWheelService->canSpin($user);
        try { $this->streakService->checkAndReward($user); } catch (\Exception $e) {}
        return view('lottery.index', compact('game', 'balance', 'history', 'canFreeSpin', 'freeSpinHours', 'leaderboard',
            'activeTournament', 'completedMissions', 'totalMissions', 'canSpinBonusWheel', 'symbols'));
    }

    public function spin(Request $request)
    {
        $this->checkRateLimit();
        $request->validate(['bet' => 'required|numeric|min:1', 'client_seed' => 'nullable|string']);
        $service = new LotteryService($this->game);
        try {
            $result = $service->play(Auth::user(), $request->bet, false, $request->client_seed);
            return response()->json(['success' => true,
                'symbols' => array_map(fn($s) => ['name' => $s->name, 'display_name' => $s->display_name, 'icon' => $s->icon], $result['symbols']),
                'win_amount' => $result['win_amount'], 'net_change' => $result['net_change'],
                'mini_jackpot' => $result['mini_jackpot'], 'super_jackpot' => $result['super_jackpot'],
                'free_spin_trigger' => $result['free_spin_trigger'], 'progressive_jackpot' => $result['progressive_jackpot'],
                'new_balance' => Auth::user()->wallet->fresh()->balance,
                'nonce' => $result['nonce'], 'client_seed' => $result['client_seed'], 'server_seed_hashed' => $result['server_seed_hashed'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function freeSpin(Request $request)
    {
        $this->checkRateLimit();
        $service = new LotteryService($this->game);
        if (!$this->game->enable_free_spins) return response()->json(['success' => false, 'message' => 'Free spins disabled'], 422);
        if (!$service->canUseFreeSpin(Auth::user())) return response()->json(['success' => false, 'message' => 'Free spin already used today.'], 422);
        try {
            $result = $service->play(Auth::user(), 0, true, $request->client_seed ?? null);
            return response()->json(['success' => true, 'symbols' => array_map(fn($s) => ['name' => $s->name, 'display_name' => $s->display_name, 'icon' => $s->icon], $result['symbols']),
                'win_amount' => $result['win_amount'], 'net_change' => $result['net_change'],
                'mini_jackpot' => $result['mini_jackpot'], 'super_jackpot' => $result['super_jackpot'],
                'free_spin_trigger' => $result['free_spin_trigger'], 'progressive_jackpot' => $result['progressive_jackpot'],
                'new_balance' => Auth::user()->wallet->fresh()->balance,
                'nonce' => $result['nonce'], 'client_seed' => $result['client_seed'], 'server_seed_hashed' => $result['server_seed_hashed'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function buyBonus(Request $request)
    {
        $price = $this->game->getBonusBuyPrice();
        if (!$price) return response()->json(['success' => false, 'message' => 'Bonus buy not available'], 422);
        $request->validate(['cost' => 'required|numeric|min:' . $price]);
        $user = Auth::user();
        if ($user->wallet->balance < $request->cost) return response()->json(['success' => false, 'message' => 'Insufficient balance'], 422);
        DB::transaction(function () use ($user, $request) {
            $user->wallet->decrement('balance', $request->cost);
            $user->transactions()->create(['type' => 'bonus_buy', 'amount' => -$request->cost, 'status' => 'completed',
                'description' => 'Bonus Buy: 10 Free Spins', 'balance_after' => $user->wallet->balance,
                'user_id' => $user->id, 'wallet_id' => $user->wallet->id]);
            $user->free_spins_available = ($user->free_spins_available ?? 0) + 10;
            $user->save();
        });
        return response()->json(['success' => true, 'free_spins' => 10]);
    }

    public function gamble(Request $request) { /* existing */ }
    public function history() { $history = LotterySpin::where('user_id', Auth::id())->latest()->paginate(20); return view('lottery.history', compact('history')); }
    public function leaderboard($period = 'weekly') { /* existing */ }
    public function achievements() { /* existing */ }
    public function dashboard() { /* existing */ }
    public function verifySpin(Request $request) { /* existing */ }
}
