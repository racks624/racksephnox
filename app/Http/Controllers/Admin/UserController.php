<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
        }
        $users = $query->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['wallet', 'investments', 'transactions', 'machineInvestments']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string',
            'is_admin' => 'boolean',
            'is_verified' => 'boolean',
            'kyc_status' => 'in:pending,verified,rejected',
        ]);
        $user->update($validated);
        return redirect()->route('admin.users.show', $user)->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    public function toggleAdmin(User $user)
    {
        $user->is_admin = !$user->is_admin;
        $user->save();
        return back()->with('success', 'Admin status toggled.');
    }
}
