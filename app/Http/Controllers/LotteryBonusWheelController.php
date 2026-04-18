<?php

namespace App\Http\Controllers;

use App\Services\LotteryBonusWheelService;
use Illuminate\Http\Request;

class LotteryBonusWheelController extends Controller
{
    public function index()
    {
        $service = new LotteryBonusWheelService();
        $canSpin = $service->canSpin(auth()->user());
        return view('lottery.bonus-wheel', compact('canSpin'));
    }

    public function spin()
    {
        $service = new LotteryBonusWheelService();
        try {
            $prize = $service->spin(auth()->user());
            return response()->json([
                'success' => true,
                'prize' => $prize,
                'new_balance' => auth()->user()->wallet->fresh()->balance,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
