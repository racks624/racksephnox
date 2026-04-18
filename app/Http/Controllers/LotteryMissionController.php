<?php

namespace App\Http\Controllers;

use App\Services\LotteryMissionService;
use Illuminate\Http\Request;

class LotteryMissionController extends Controller
{
    public function index()
    {
        $service = new LotteryMissionService();
        $missions = $service->getTodayMissions(auth()->user());
        return view('lottery.missions', compact('missions'));
    }
}
