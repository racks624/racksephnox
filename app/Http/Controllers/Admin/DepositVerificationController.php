<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use App\Models\User;
use App\Notifications\DepositNotification;
use App\Notifications\BonusNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DepositVerificationController extends Controller
{
    public function index()
    {
        $pending = DepositRequest::where('status', 'pending')
            ->where('created_at', '>=', Carbon::now()->subHours(config('deposit.request_expiry_hours', 48)))
            ->with('user')
            ->latest()
            ->paginate(20);
            
        $expired = DepositRequest::where('status', 'pending')
            ->where('created_at', '<', Carbon::now()->subHours(config('deposit.request_expiry_hours', 48)))
            ->update(['status' => 'expired']);
            
        return view('admin.deposits.index', compact('pending'));
    }

    public function verify(DepositRequest $deposit)
    {
        $deposit->update([
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        $user = $deposit->user;
        
        // Credit deposit amount
        $user->wallet->credit($deposit->amount, 'Deposit verified by admin');
        
        // Apply deposit bonuses
        $depositCount = $user->depositRequests()->where('status', 'verified')->count();
        
        if ($depositCount === 1) {
            // First deposit bonus
            $user->wallet->credit(config('deposit.first_deposit_bonus', 40), 'First deposit bonus');
            $user->notify(new BonusNotification(config('deposit.first_deposit_bonus', 40), 'first_deposit'));
        } else {
            // Consecutive deposit bonus
            $user->wallet->credit(config('deposit.consecutive_deposit_bonus', 20), 'Consecutive deposit bonus');
            $user->notify(new BonusNotification(config('deposit.consecutive_deposit_bonus', 20), 'deposit_bonus'));
        }
        
        // Send deposit notification
        $user->notify(new DepositNotification($deposit->amount, 'Admin Verified'));

        return redirect()->route('admin.deposits.index')->with('success', 'Deposit verified and credited.');
    }

    public function reject(Request $request, DepositRequest $deposit)
    {
        $request->validate(['reason' => 'required|string']);
        $deposit->update([
            'status' => 'rejected',
            'admin_notes' => $request->reason,
        ]);
        return redirect()->route('admin.deposits.index')->with('success', 'Deposit rejected.');
    }
}
