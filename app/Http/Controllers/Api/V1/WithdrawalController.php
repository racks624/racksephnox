<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Services\WithdrawalService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    use ApiResponse;

    public function submitRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:530|max:1000000',
            'phone' => 'required|string|regex:/^254[0-9]{9}$/',
        ]);

        $amount = $request->amount;
        $validation = WithdrawalService::validateWithdrawal($amount);
        if (!$validation['valid']) {
            return $this->errorResponse($validation['message'], 422);
        }

        $fee = WithdrawalService::calculateFee($amount);
        $netAmount = $amount - $fee;
        $user = $request->user();

        if ($user->wallet->balance < $amount) {
            return $this->errorResponse('Insufficient wallet balance.', 422);
        }

        $user->wallet->debit($amount, 'Withdrawal request: ' . $amount . ' (fee: ' . $fee . ')');

        $withdrawal = WithdrawalRequest::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'fee' => $fee,
            'net_amount' => $netAmount,
            'phone' => $request->phone,
            'status' => 'pending',
        ]);

        return $this->successResponse($withdrawal, 'Withdrawal request submitted.', 201);
    }

    public function history(Request $request)
    {
        $withdrawals = $request->user()->withdrawalRequests()->latest()->paginate(20);
        return $this->successResponse($withdrawals);
    }
}
