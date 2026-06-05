<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\LotteryGame;
use App\Models\LotterySpin;
use App\Models\LotteryAchievement;
use App\Models\LotteryUserAchievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LotteryController extends Controller
{
    public function jackpot() { $game = LotteryGame::where('is_active', true)->first(); return response()->json(['jackpot' => $game ? $game->progressive_jackpot : 1000]); }
    public function leaderboard(Request $request) {
        $period = $request->get('period', 'weekly');
        $startDate = match($period) { 'weekly' => now()->startOfWeek(), 'monthly' => now()->startOfMonth(), 'all' => now()->subYear(), default => now()->subDays(7) };
        $data = LotterySpin::where('created_at', '>=', $startDate)->where('win_amount', '>', 0)
            ->selectRaw('user_id, SUM(win_amount) as total_win')->groupBy('user_id')->orderBy('total_win', 'desc')
            ->with('user')->take(20)->get()->map(fn($i) => ['user_name' => $i->user->name, 'user_avatar' => $i->user->avatar, 'total_win' => $i->total_win]);
        return response()->json(['success' => true, 'data' => $data]);
    }
    public function achievements(Request $request) {
        $user = Auth::user();
        $earned = LotteryUserAchievement::where('user_id', $user->id)->with('achievement')->get()->map(fn($i) => ['name' => $i->achievement->name, 'description' => $i->achievement->description, 'icon' => $i->achievement->icon, 'achieved_at' => $i->achieved_at]);
        $all = LotteryAchievement::all()->map(fn($a) => ['id' => $a->id, 'name' => $a->name, 'description' => $a->description, 'icon' => $a->icon, 'reward_free_spins' => $a->reward_free_spins]);
        return response()->json(['success' => true, 'earned' => $earned, 'all' => $all]);
    }
    public function recentWins(Request $request) {
        $wins = LotterySpin::where('win_amount', '>', 0)->with('user')->latest()->take($request->get('limit', 10))->get()
            ->map(fn($s) => ['user_name' => $s->user->name, 'amount' => $s->win_amount, 'is_jackpot' => $s->super_jackpot_hit || $s->mini_jackpot_hit, 'time_ago' => $s->created_at->diffForHumans()]);
        return response()->json(['success' => true, 'data' => $wins]);
    }
    public function myStats(Request $request) {
        $user = Auth::user();
        $stats = ['total_spins' => LotterySpin::where('user_id', $user->id)->count(), 'total_bet' => LotterySpin::where('user_id', $user->id)->sum('bet_amount'),
            'total_win' => LotterySpin::where('user_id', $user->id)->sum('win_amount'), 'mini_jackpots' => LotterySpin::where('user_id', $user->id)->where('mini_jackpot_hit', true)->count(),
            'super_jackpots' => LotterySpin::where('user_id', $user->id)->where('super_jackpot_hit', true)->count(), 'free_spins_used' => LotterySpin::where('user_id', $user->id)->where('free_spin_used', true)->count()];
        $stats['net_profit'] = $stats['total_win'] - $stats['total_bet'];
        $stats['win_rate'] = $stats['total_spins'] > 0 ? round(($stats['total_win'] / $stats['total_bet']) * 100, 2) : 0;
        return response()->json(['success' => true, 'data' => $stats]);
    }
}
