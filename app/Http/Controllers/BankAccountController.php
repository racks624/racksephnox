<?php

namespace App\Http\Controllers;

use App\Models\UserBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    public function index()
    {
        $accounts = Auth::user()->bankAccounts()->get();
        return view('bank_accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('bank_accounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'is_default' => 'boolean',
        ]);

        $user = Auth::user();
        
        if ($request->is_default) {
            $user->bankAccounts()->update(['is_default' => false]);
        }
        
        $user->bankAccounts()->create($request->all());
        
        return redirect()->route('bank-accounts.index')->with('success', 'Bank account added successfully.');
    }

    public function edit(UserBankAccount $bankAccount)
    {
        $this->authorize('update', $bankAccount);
        return view('bank_accounts.edit', compact('bankAccount'));
    }

    public function update(Request $request, UserBankAccount $bankAccount)
    {
        $this->authorize('update', $bankAccount);
        
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'is_default' => 'boolean',
        ]);
        
        if ($request->is_default) {
            Auth::user()->bankAccounts()->where('id', '!=', $bankAccount->id)->update(['is_default' => false]);
        }
        
        $bankAccount->update($request->all());
        
        return redirect()->route('bank-accounts.index')->with('success', 'Bank account updated.');
    }

    public function destroy(UserBankAccount $bankAccount)
    {
        $this->authorize('delete', $bankAccount);
        $bankAccount->delete();
        return redirect()->route('bank-accounts.index')->with('success', 'Bank account deleted.');
    }
}
