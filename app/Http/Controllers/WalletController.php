<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $wallet = $user->wallet;
        $transactions = $user->transactions()->latest()->paginate(15);
        return view('wallet.show', compact('wallet', 'transactions'));
    }
}
