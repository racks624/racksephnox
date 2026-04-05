@extends('layouts.app')

@section('content')
<div x-data="profileManager()" x-init="init()" class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Profile Header -->
        <div class="card-golden p-6 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-20 h-20 rounded-full bg-gold/20 flex items-center justify-center text-4xl text-gold">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gold">{{ $user->name }}</h1>
                    <p class="text-ivory/70">{{ $user->email }} | {{ $user->phone }}</p>
                    <p class="text-sm text-gold-400 mt-1">Member since {{ $user->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="card-golden p-4 text-center">
                <i class="fas fa-wallet text-2xl text-gold"></i>
                <p class="text-sm text-ivory/60 mt-1">Wallet Balance</p>
                <p class="text-xl font-bold text-gold">KES {{ number_format($wallet->balance, 2) }}</p>
            </div>
            <div class="card-golden p-4 text-center">
                <i class="fas fa-chart-line text-2xl text-gold"></i>
                <p class="text-sm text-ivory/60 mt-1">Trading Balance</p>
                <p class="text-xl font-bold text-gold">KES {{ number_format($tradingAccount->balance ?? 0, 2) }}</p>
            </div>
            <div class="card-golden p-4 text-center">
                <i class="fas fa-users text-2xl text-gold"></i>
                <p class="text-sm text-ivory/60 mt-1">Referrals</p>
                <p class="text-xl font-bold text-gold">{{ number_format($referralCount) }}</p>
            </div>
        </div>

        <!-- Tabs -->
        <div class="card-golden p-6">
            <div class="flex border-b border-gold/30 mb-6">
                <button @click="activeTab = 'profile'" :class="{'border-gold text-gold': activeTab === 'profile'}" class="px-4 py-2 border-b-2 border-transparent">Profile Settings</button>
                <button @click="activeTab = 'security'" :class="{'border-gold text-gold': activeTab === 'security'}" class="px-4 py-2 border-b-2 border-transparent">Security</button>
                <button @click="activeTab = 'notifications'" :class="{'border-gold text-gold': activeTab === 'notifications'}" class="px-4 py-2 border-b-2 border-transparent">Notifications</button>
                <button @click="activeTab = 'danger'" :class="{'border-gold text-gold': activeTab === 'danger'}" class="px-4 py-2 border-b-2 border-transparent">Danger Zone</button>
            </div>

            <!-- Profile Settings Tab -->
            <div x-show="activeTab === 'profile'">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label class="block text-sm text-gold-400 mb-1">Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-golden w-full" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm text-gold-400 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-golden w-full" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm text-gold-400 mb-1">Phone</label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="input-golden w-full" required>
                    </div>
                    <button type="submit" class="btn-golden">Save Changes</button>
                </form>
            </div>

            <!-- Security Tab -->
            <div x-show="activeTab === 'security'">
                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm text-gold-400 mb-1">Current Password</label>
                        <input type="password" name="current_password" class="input-golden w-full" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm text-gold-400 mb-1">New Password</label>
                        <input type="password" name="password" class="input-golden w-full" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm text-gold-400 mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="input-golden w-full" required>
                    </div>
                    <button type="submit" class="btn-golden">Update Password</button>
                </form>
            </div>

            <!-- Notifications Tab -->
            <div x-show="activeTab === 'notifications'">
                <form method="POST" action="{{ route('profile.notifications.update') }}">
                    @csrf
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gold">Email Notifications</h3>
                        <label class="flex items-center justify-between">
                            <span>Deposit Confirmations</span>
                            <input type="checkbox" name="email_deposit" value="1" {{ ($user->notification_preferences['email_deposit'] ?? true) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold">
                        </label>
                        <label class="flex items-center justify-between">
                            <span>Investment Updates</span>
                            <input type="checkbox" name="email_investment" value="1" {{ ($user->notification_preferences['email_investment'] ?? true) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold">
                        </label>
                        <label class="flex items-center justify-between">
                            <span>Withdrawal Status</span>
                            <input type="checkbox" name="email_withdrawal" value="1" {{ ($user->notification_preferences['email_withdrawal'] ?? true) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-gold">
                        </label>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="btn-golden">Save Preferences</button>
                    </div>
                </form>
            </div>

            <!-- Danger Zone Tab -->
            <div x-show="activeTab === 'danger'">
                <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-red-400">Delete Account</h3>
                    <p class="text-sm text-ivory/70 mb-4">Once deleted, all your data will be permanently removed. This action cannot be undone.</p>
                    <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure you want to delete your account?');">
                        @csrf
                        @method('DELETE')
                        <div class="mb-4">
                            <label class="block text-sm text-red-400 mb-1">Confirm Password</label>
                            <input type="password" name="password" class="input-golden w-full" required>
                        </div>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Delete Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function profileManager() {
    return {
        activeTab: 'profile',
        init() {}
    }
}
</script>
@endsection
