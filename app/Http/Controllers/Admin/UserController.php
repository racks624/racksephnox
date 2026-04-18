<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        $users = $query->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
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
            'email' => 'required|email|unique:users,email,' . $user->id,
            'is_admin' => 'boolean',
            'is_verified' => 'boolean',
            'kyc_status' => 'string|in:pending,approved,rejected',
        ]);
        $user->update($validated);
        return redirect()->route('admin.users.index')->with('success', 'User updated.');
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

    public function toggleVip(User $user)
    {
        $user->is_vip = !$user->is_vip;
        $user->save();
        return back()->with('success', 'VIP status updated.');
    }

    public function togglePromo(User $user)
    {
        $user->has_promo = !$user->has_promo;
        if ($user->has_promo) {
            $user->promo_expires_at = now()->addDays(7);
        } else {
            $user->promo_expires_at = null;
        }
        $user->save();
        return back()->with('success', 'Promo status updated.');
    }

    public function export()
    {
        $users = User::all();
        $filename = 'users_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://output', 'w');
        ob_start();
        fputcsv($handle, ['ID', 'Name', 'Email', 'Is Admin', 'Is VIP', 'Has Promo', 'KYC Status', 'Joined']);
        foreach ($users as $user) {
            fputcsv($handle, [
                $user->id,
                $user->name,
                $user->email,
                $user->is_admin ? 'Yes' : 'No',
                $user->is_vip ? 'Yes' : 'No',
                $user->has_promo ? 'Yes' : 'No',
                $user->kyc_status ?? 'pending',
                $user->created_at->format('Y-m-d'),
            ]);
        }
        $csv = ob_get_clean();
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
