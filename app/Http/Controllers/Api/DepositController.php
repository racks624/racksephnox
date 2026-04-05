<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function getPochiNumber()
    {
        return response()->json([
            'status' => 'success',
            'data' => ['phone_number' => config('deposit.default_phone')]
        ]);
    }

    public function submitRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10|max:500000',
            'transaction_reference' => 'required|string|unique:deposit_requests',
        ]);

        $user = $request->user();
        $phoneNumber = config('deposit.default_phone');

        $deposit = DepositRequest::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'transaction_reference' => $request->transaction_reference,
            'phone_number' => $phoneNumber,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Deposit request submitted. Awaiting admin verification.',
            'data' => $deposit
        ], 201);
    }

    public function history(Request $request)
    {
        $deposits = $request->user()->depositRequests()->latest()->paginate(20);
        return response()->json([
            'status' => 'success',
            'data' => $deposits
        ]);
    }
}
