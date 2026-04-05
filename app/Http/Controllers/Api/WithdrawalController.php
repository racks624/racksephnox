<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Services\WithdrawalService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function submitRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:530|max:1000000',
            'phone' => 'required|string|regex:/^254[0-9]{9}$/',
        ]);

        $amount = $request->amount;
        $validation = WithdrawalService::validateWithdrawal($amount);
        if (!$validation['valid']) {
            return response()->json([
                'status' => 'error',
                'message' => $validation['message']
            ], 422);
        }

        $fee = WithdrawalService::calculateFee($amount);
        $netAmount = WithdrawalService::getNetAmount($amount);
        $user = $request->user();

        if ($user->wallet->balance < $amount) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient wallet balance.'
            ], 422);
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

        return response()->json([
            'status' => 'success',
            'message' => 'Withdrawal request submitted.',
            'data' => $withdrawal
        ], 201);
    }

    public function history(Request $request)
    {
        $withdrawals = $request->user()->withdrawalRequests()->latest()->paginate(20);
        return response()->json([
            'status' => 'success',
            'data' => $withdrawals
        ]);
    }
}
