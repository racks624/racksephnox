<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('wallet');
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }
        $users = $query->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('wallet', 'investments.plan', 'transactions', 'kycDocuments');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|unique:users,phone,' . $user->id,
            'is_admin' => 'boolean',
            'is_verified' => 'boolean',
            'kyc_level' => 'in:basic,tier1,tier2',
        ]);

        $user->update($request->only('name', 'email', 'phone', 'is_admin', 'is_verified', 'kyc_level'));

        // Clear admin stats cache
        Cache::forget('admin_stats');

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        Cache::forget('admin_stats');
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
