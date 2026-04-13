<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\UserBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    public function submitRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:530|max:1000000',
            'bank_account_id' => 'required|exists:user_bank_accounts,id',
        ]);

        $user = Auth::user();
        
        if ($user->wallet->balance < $request->amount) {
            return response()->json(['success' => false, 'message' => 'Insufficient balance'], 422);
        }

        $withdrawal = WithdrawalRequest::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'bank_account_id' => $request->bank_account_id,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal request submitted successfully',
            'data' => $withdrawal,
        ]);
    }

    public function history()
    {
        $withdrawals = WithdrawalRequest::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(['success' => true, 'data' => $withdrawals]);
    }

    public function status($id)
    {
        $withdrawal = WithdrawalRequest::where('user_id', Auth::id())->findOrFail($id);
        return response()->json(['success' => true, 'data' => $withdrawal]);
    }

    public function addBankAccount(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
        ]);

        $account = UserBankAccount::create([
            'user_id' => Auth::id(),
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
        ]);

        return response()->json(['success' => true, 'data' => $account]);
    }

    public function removeBankAccount($id)
    {
        $account = UserBankAccount::where('user_id', Auth::id())->findOrFail($id);
        $account->delete();
        return response()->json(['success' => true, 'message' => 'Bank account removed']);
    }
}
