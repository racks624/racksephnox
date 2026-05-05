<?php
namespace App\Http\Controllers;
use App\Models\LotterySpin;
class LotterySocialController extends Controller
{
    public function feed() {
        $recentWins = LotterySpin::where('win_amount', '>', 0)->with('user')->latest()->take(20)->get();
        return view('lottery.social', compact('recentWins'));
    }
}
