<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form with all data
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Load relationships for profile data
        $user->load(['wallet', 'tradingAccount', 'bankAccounts', 'kycDocuments']);
        
        // Get user statistics
        $stats = [
            'total_invested' => $user->investments()->sum('amount'),
            'total_machine_invested' => $user->machineInvestments()->sum('amount'),
            'total_referrals' => $user->referrals()->count(),
            'total_bonus' => $user->transactions()->where('type', 'referral_bonus')->sum('amount'),
            'total_interest' => $user->transactions()->where('type', 'interest')->sum('amount'),
            'member_since' => $user->created_at->format('F Y'),
        ];
        
        // Get recent activities
        $recentActivities = $user->transactions()
            ->latest()
            ->take(10)
            ->get();
        
        // Get KYC status badge
        $kycBadge = $this->getKycBadge($user->kyc_status ?? 'pending');
        
        return view('profile.edit', compact('user', 'stats', 'recentActivities', 'kycBadge'));
    }

    /**
     * Update the user's profile information
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        $validated = $request->validated();
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $request->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }
        
        // Handle phone formatting
        if (isset($validated['phone'])) {
            $validated['phone'] = $this->formatPhoneNumber($validated['phone']);
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
     * Update user's password
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        
        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();
        
        return back()->with('status', 'password-updated');
    }

    /**
     * Update user's notification preferences
     */
    public function updateNotificationPreferences(Request $request): RedirectResponse
    {
        $request->validate([
            'email_deposit' => 'sometimes|boolean',
            'email_investment' => 'sometimes|boolean',
            'email_withdrawal' => 'sometimes|boolean',
            'email_trading' => 'sometimes|boolean',
            'database_deposit' => 'sometimes|boolean',
            'database_investment' => 'sometimes|boolean',
            'database_withdrawal' => 'sometimes|boolean',
            'database_trading' => 'sometimes|boolean',
            'broadcast_deposit' => 'sometimes|boolean',
            'broadcast_investment' => 'sometimes|boolean',
            'broadcast_withdrawal' => 'sometimes|boolean',
            'broadcast_trading' => 'sometimes|boolean',
            'daily_digest' => 'sometimes|boolean',
            'weekly_report' => 'sometimes|boolean',
        ]);
        
        $user = $request->user();
        $preferences = $request->only([
            'email_deposit', 'email_investment', 'email_withdrawal', 'email_trading',
            'database_deposit', 'database_investment', 'database_withdrawal', 'database_trading',
            'broadcast_deposit', 'broadcast_investment', 'broadcast_withdrawal', 'broadcast_trading',
            'daily_digest', 'weekly_report',
        ]);
        
        // Ensure boolean values
        foreach ($preferences as $key => $value) {
            $preferences[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
        
        $user->notification_preferences = $preferences;
        $user->save();
        
        return back()->with('status', 'preferences-updated');
    }

    /**
     * Update user's bank account information
     */
    public function updateBankAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
        ]);
        
        $user = $request->user();
        
        $user->bankAccount()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
            ]
        );
        
        return back()->with('status', 'bank-account-updated');
    }

    /**
     * Delete the user's account
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);
        
        $user = $request->user();
        
        DB::beginTransaction();
        
        try {
            // Delete user's data
            $user->transactions()->delete();
            $user->investments()->delete();
            $user->machineInvestments()->delete();
            $user->wallet()->delete();
            $user->tradingAccount()->delete();
            $user->notifications()->delete();
            
            // Delete avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            Auth::logout();
            
            $user->delete();
            
            DB::commit();
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return Redirect::to('/');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete account: ' . $e->getMessage()]);
        }
    }

    /**
     * Get user's API data (for AJAX)
     */
    public function apiData(Request $request)
    {
        $user = $request->user();
        $user->load(['wallet', 'tradingAccount']);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
                'kyc_status' => $user->kyc_status,
                'wallet_balance' => $user->wallet->balance ?? 0,
                'trading_balance' => $user->tradingAccount->balance ?? 0,
                'created_at' => $user->created_at->toIso8601String(),
            ]
        ]);
    }

    /**
     * Get KYC badge HTML
     */
    private function getKycBadge($status)
    {
        $badges = [
            'verified' => '<span class="px-2 py-1 text-xs rounded-full bg-green-500/20 text-green-400"><i class="fas fa-check-circle mr-1"></i>Verified</span>',
            'pending' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-500/20 text-yellow-400"><i class="fas fa-clock mr-1"></i>Pending</span>',
            'rejected' => '<span class="px-2 py-1 text-xs rounded-full bg-red-500/20 text-red-400"><i class="fas fa-times-circle mr-1"></i>Rejected</span>',
            'not_submitted' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-500/20 text-gray-400"><i class="fas fa-upload mr-1"></i>Not Submitted</span>',
        ];
        
        return $badges[$status] ?? $badges['not_submitted'];
    }

    /**
     * Format phone number to international format
     */
    private function formatPhoneNumber($phone)
    {
        // Remove any non-digit characters
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
