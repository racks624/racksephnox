<?php

namespace App\Http\Controllers;

use App\Models\WithdrawalRequest;
use App\Services\WithdrawalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    public function showForm()
    {
        $user = Auth::user();
        $minWithdrawal = env('MIN_WITHDRAWAL', 530);
        $maxWithdrawal = 1000000;
        return view('withdrawal.form', compact('user', 'minWithdrawal', 'maxWithdrawal'));
    }

    public function submitRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:530|max:1000000',
            'phone' => 'required|string|regex:/^254[0-9]{9}$/',
        ]);

        $amount = $request->amount;
        $validation = WithdrawalService::validateWithdrawal($amount);
        if (!$validation['valid']) {
            return back()->withErrors(['error' => $validation['message']]);
        }

        $fee = WithdrawalService::calculateFee($amount);
        $netAmount = WithdrawalService::getNetAmount($amount);
        $user = Auth::user();

        if ($user->wallet->balance < $amount) {
            return back()->withErrors(['error' => 'Insufficient wallet balance.']);
        }

        // Debit wallet immediately (pending verification)
        $user->wallet->debit($amount, 'Withdrawal request: ' . $amount . ' (fee: ' . $fee . ')');

        $withdrawal = WithdrawalRequest::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'fee' => $fee,
            'net_amount' => $netAmount,
            'phone' => $request->phone,
            'status' => 'pending',
        ]);

        return redirect()->route('withdrawal.history')->with('success', 'Withdrawal request submitted. Awaiting processing.');
    }

    public function history()
    {
        $withdrawals = Auth::user()->withdrawalRequests()->latest()->paginate(10);
        return view('withdrawal.history', compact('withdrawals'));
    }
}
