<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $wallet = $user->wallet;
        $tradingAccount = $user->tradingAccount;
        $referralCount = $user->referrals()->count();
        
        return view('profile.edit', compact('user', 'wallet', 'tradingAccount', 'referralCount'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function updateNotificationPreferences(Request $request): RedirectResponse
    {
        $preferences = $request->only([
            'email_deposit', 'email_investment', 'email_withdrawal',
            'database_deposit', 'database_investment', 'database_withdrawal',
            'broadcast_deposit', 'broadcast_investment', 'broadcast_withdrawal',
        ]);
        
        $request->user()->notification_preferences = $preferences;
        $request->user()->save();

        return back()->with('success', 'Notification preferences updated.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
