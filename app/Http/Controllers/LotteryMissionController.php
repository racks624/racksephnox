<?php
namespace App\Http\Controllers;
use App\Services\LotteryMissionService;
class LotteryMissionController extends Controller
{
    public function index() {
        $missions = (new LotteryMissionService())->getTodayMissions(auth()->user());
        return view('lottery.missions', compact('missions'));
    }
}
