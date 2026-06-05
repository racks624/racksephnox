<?php
namespace App\Http\Controllers;

use App\Models\DepositRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepositController extends Controller
{
    public function form()
    {
        return view('deposit.form');
    }

    public function submit(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:10|max:500000', 'transaction_code' => 'required|string|unique:deposit_requests']);
        DepositRequest::create(['user_id' => Auth::id(), 'amount' => $request->amount, 'transaction_code' => $request->transaction_code, 'status' => 'pending']);
        return back()->with('success', 'Deposit request submitted. Awaiting confirmation.');
    }
}
