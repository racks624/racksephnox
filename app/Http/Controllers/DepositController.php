<?php

namespace App\Http\Controllers;

use App\Models\DepositRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepositController extends Controller
{
    public function showForm()
    {
        $selectedNumber = config('deposit.default_phone');
        $minDeposit = config('deposit.min_deposit');
        $maxDeposit = config('deposit.max_deposit');
        return view('deposit.form', compact('selectedNumber', 'minDeposit', 'maxDeposit'));
    }

    public function submitRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:' . config('deposit.min_deposit') . '|max:' . config('deposit.max_deposit'),
            'transaction_reference' => 'required|string|unique:deposit_requests',
        ]);

        $user = Auth::user();
        $phoneNumber = config('deposit.default_phone');

        $deposit = DepositRequest::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'transaction_reference' => $request->transaction_reference,
            'phone_number' => $phoneNumber,
            'status' => 'pending',
        ]);

        return redirect()->route('deposit.status')->with('success', 'Deposit request submitted. Awaiting admin verification.');
    }

    public function status()
    {
        $deposits = Auth::user()->depositRequests()->latest()->paginate(10);
        $expiryHours = config('deposit.request_expiry_hours');
        return view('deposit.status', compact('deposits', 'expiryHours'));
    }
}
