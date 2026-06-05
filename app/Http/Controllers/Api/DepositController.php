<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepositController extends Controller
{
    public function getPochiNumber()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'paybill_number' => config('mpesa.deposit_number', '+254111516744'),
                'account_name' => 'Racksephnox Deposit',
            ]
        ]);
    }

    public function submitRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10|max:500000',
            'transaction_code' => 'required|string|unique:deposit_requests',
        ]);

        $deposit = DepositRequest::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'transaction_code' => $request->transaction_code,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Deposit request submitted successfully',
            'data' => $deposit,
        ]);
    }

    public function history()
    {
        $deposits = DepositRequest::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(['success' => true, 'data' => $deposits]);
    }

    public function status($id)
    {
        $deposit = DepositRequest::where('user_id', Auth::id())->findOrFail($id);
        return response()->json(['success' => true, 'data' => $deposit]);
    }

    public function verifyPayment(Request $request)
    {
        // This would typically be called by M-Pesa callback
        return response()->json(['success' => true, 'message' => 'Payment verification endpoint']);
    }
}
