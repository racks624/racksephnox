<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserBankAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form with all data.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load('bankAccounts');
        $bankAccounts = $user->bankAccounts;
        $notificationPreferences = $user->notification_preferences ?? [
            'email_deposit' => true,
            'email_investment' => true,
            'email_withdrawal' => true,
            'email_trading' => true,
            'database_deposit' => true,
            'database_investment' => true,
            'database_withdrawal' => true,
            'database_trading' => true,
            'sms_deposit' => true,
            'sms_withdrawal' => true,
        ];
        $twoFactorEnabled = !is_null($user->two_factor_confirmed_at);

        return view('profile.edit', compact('user', 'bankAccounts', 'notificationPreferences', 'twoFactorEnabled'));
    }

    /**
     * Update the user's profile information (name, email, phone, avatar).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $request->validate(['avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048']);
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        // Normalize phone number (if present)
        if (isset($validated['phone'])) {
            $validated['phone'] = $this->normalizePhone($validated['phone']);
        }

        $user->fill($validated);
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        // Clear user cache
        Cache::forget("user_{$user->id}_profile");

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('status', 'password-updated');
    }

    /**
     * Delete the user's account.
     */
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

        return Redirect::to('/');
    }

    /**
     * Add a new bank account.
     */
    public function addBankAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50|unique:user_bank_accounts',
        ]);

        $request->user()->bankAccounts()->create($request->only(['bank_name', 'account_name', 'account_number']));

        return back()->with('status', 'bank-account-added');
    }

    /**
     * Update a bank account.
     */
    public function updateBankAccount(Request $request, UserBankAccount $bankAccount): RedirectResponse
    {
        if ($bankAccount->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50|unique:user_bank_accounts,account_number,' . $bankAccount->id,
        ]);

        $bankAccount->update($request->only(['bank_name', 'account_name', 'account_number']));

        return back()->with('status', 'bank-account-updated');
    }

    /**
     * Delete a bank account.
     */
    public function deleteBankAccount(UserBankAccount $bankAccount): RedirectResponse
    {
        if ($bankAccount->user_id !== Auth::id()) {
            abort(403);
        }
        $bankAccount->delete();

        return back()->with('status', 'bank-account-deleted');
    }

    /**
     * Update notification preferences.
     */
    public function updateNotificationPreferences(Request $request): RedirectResponse
    {
        $preferences = $request->only([
            'email_deposit', 'email_investment', 'email_withdrawal', 'email_trading',
            'database_deposit', 'database_investment', 'database_withdrawal', 'database_trading',
            'sms_deposit', 'sms_withdrawal',
        ]);

        // Ensure boolean values
        foreach ($preferences as $key => $value) {
            $preferences[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        $user = $request->user();
        $user->notification_preferences = $preferences;
        $user->save();

        return back()->with('status', 'preferences-updated');
    }

    /**
     * Enable/disable two‑factor authentication.
     */
    public function toggleTwoFactor(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->two_factor_confirmed_at) {
            $user->two_factor_confirmed_at = null;
            $user->two_factor_secret = null;
            $user->two_factor_recovery_codes = null;
            $user->save();
            return back()->with('status', 'two-factor-disabled');
        } else {
            // In a real implementation, you would generate secret and show QR code.
            // For now, we just mark it enabled (simplified).
            $user->two_factor_confirmed_at = now();
            $user->save();
            return back()->with('status', 'two-factor-enabled');
        }
    }

    /**
     * Normalize Kenyan phone number to +254XXXXXXXXX format.
     */
    private function normalizePhone($phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (substr($digits, 0, 1) === '0') {
            return '+254' . substr($digits, 1);
        }
        if (strlen($digits) === 9) {
            return '+254' . $digits;
        }
        if (substr($digits, 0, 3) === '254') {
            return '+' . $digits;
        }
        return $digits;
    }
}
