<?php
namespace App\Http\Controllers;
use App\Services\LotteryBonusWheelService;
class LotteryBonusWheelController extends Controller
{
    public function index() {
        $canSpin = (new LotteryBonusWheelService())->canSpin(auth()->user());
        return view('lottery.bonus-wheel', compact('canSpin'));
    }
    public function spin() {
        try {
            $prize = (new LotteryBonusWheelService())->spin(auth()->user());
            return response()->json(['success' => true, 'prize' => $prize]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
