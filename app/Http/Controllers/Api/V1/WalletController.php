<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    use ApiResponse;

    public function balance(Request $request)
    {
        $user = $request->user();
        return $this->successResponse([
            'balance' => $user->wallet->balance,
            'locked_balance' => $user->wallet->locked_balance,
        ]);
    }

    public function transactions(Request $request)
    {
        $transactions = $request->user()->transactions()->latest()->paginate(20);
        return $this->successResponse($transactions);
    }
}
