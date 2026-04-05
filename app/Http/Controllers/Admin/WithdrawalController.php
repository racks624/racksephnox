<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Notifications\WithdrawalNotification;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        $pending = WithdrawalRequest::where('status', 'pending')->with('user')->latest()->paginate(20);
        return view('admin.withdrawals.index', compact('pending'));
    }

    public function process(WithdrawalRequest $withdrawal)
    {
        $withdrawal->update(['status' => 'processing']);
        return back()->with('success', 'Withdrawal marked as processing.');
    }

    public function complete(WithdrawalRequest $withdrawal)
    {
        $withdrawal->update(['status' => 'completed']);
        $withdrawal->user->notify(new WithdrawalNotification($withdrawal->amount, 'completed'));
        return back()->with('success', 'Withdrawal completed.');
    }

    public function reject(Request $request, WithdrawalRequest $withdrawal)
    {
        $request->validate(['reason' => 'required|string']);
        // Refund the amount back to user's wallet
        $withdrawal->user->wallet->credit($withdrawal->amount, 'Withdrawal request rejected: ' . $request->reason);
        $withdrawal->update([
            'status' => 'rejected',
            'admin_notes' => $request->reason,
        ]);
        $withdrawal->user->notify(new WithdrawalNotification($withdrawal->amount, 'rejected'));
        return back()->with('success', 'Withdrawal rejected and funds refunded.');
    }
}
