<?php
namespace App\Http\Controllers;

use App\Models\UserBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    public function index()
    {
        $accounts = Auth::user()->bankAccounts;
        return view('bank-accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('bank-accounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
        ]);
        Auth::user()->bankAccounts()->create($request->only('bank_name', 'account_name', 'account_number'));
        return redirect()->route('bank-accounts.index')->with('success', 'Bank account added.');
    }

    public function destroy($id)
    {
        $account = Auth::user()->bankAccounts()->findOrFail($id);
        $account->delete();
        return back()->with('success', 'Bank account removed.');
    }
}
