@extends('layouts.app')

@section('content')
<div x-data="profileManager()" x-init="init()" class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Sacred Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold golden-title">✨ Divine Profile</h1>
            <p class="text-gold-400 mt-2">Manage your sacred account information</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar (Avatar + Quick Info) -->
            <div class="lg:col-span-1">
                <div class="card-golden p-6 text-center sticky top-20">
                    <div class="relative inline-block">
                        <div class="w-32 h-32 rounded-full bg-gradient-to-r from-gold-400 to-gold-600 flex items-center justify-center mx-auto overflow-hidden">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-user-circle text-6xl text-white"></i>
                            @endif
                        </div>
                        <button @click="triggerAvatarUpload" class="absolute bottom-0 right-0 bg-gold rounded-full p-1.5 hover:bg-gold-600 transition">
                            <i class="fas fa-camera text-xs text-cosmic-void"></i>
                        </button>
                        <form id="avatar-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="hidden">
                            @csrf
                            @method('patch')
                            <input type="file" name="avatar" id="avatar-input" accept="image/*" @change="uploadAvatar">
                        </form>
                    </div>
                    <h3 class="text-xl font-bold text-gold mt-4">{{ $user->name }}</h3>
                    <p class="text-sm text-ivory/60">{{ $user->email }}</p>
                    <div class="mt-4 pt-4 border-t border-gold/20">
                        <div class="flex justify-between text-sm">
                            <span class="text-ivory/60">Member since</span>
                            <span class="text-gold">{{ $user->created_at->format('F Y') }}</span>
                        </div>
                        <div class="flex justify-between text-sm mt-2">
                            <span class="text-ivory/60">Referral code</span>
                            <span class="text-gold font-mono">{{ $user->referral_code }}</span>
                        </div>
                        <button onclick="copyReferralCode()" class="mt-4 btn-golden w-full text-sm py-2">Copy Referral Link</button>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3 space-y-6">
                
                <!-- Profile Information -->
                <div class="card-golden p-6">
                    <h2 class="text-xl font-bold text-gold mb-4 flex items-center gap-2">
                        <i class="fas fa-user-circle"></i> Profile Information
                    </h2>
                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                        @csrf
                        @method('patch')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gold-400 mb-1">Full Name</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full px-4 py-2 border border-gold/30 rounded-lg bg-cosmic-void/50 text-white focus:outline-none focus:ring-2 focus:ring-gold">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gold-400 mb-1">Email Address</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full px-4 py-2 border border-gold/30 rounded-lg bg-cosmic-void/50 text-white focus:outline-none focus:ring-2 focus:ring-gold">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gold-400 mb-1">Phone Number</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gold/30 bg-cosmic-void/50 text-gold-400">+254</span>
                                    <input type="tel" name="phone" value="{{ old('phone', preg_replace('/^\+254/', '', $user->phone)) }}"
                                        class="flex-1 px-4 py-2 border border-gold/30 rounded-r-lg bg-cosmic-void/50 text-white focus:outline-none focus:ring-2 focus:ring-gold"
                                        placeholder="712345678">
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="btn-golden px-6 py-2">
                                <i class="fas fa-save mr-2"></i> Save Changes
                            </button>
                        </div>
                        @if (session('status') === 'profile-updated')
                            <p class="text-green-400 text-sm text-center">✅ Profile updated successfully!</p>
                        @endif
                    </form>
                </div>

                <!-- Password Update -->
                <div class="card-golden p-6">
                    <h2 class="text-xl font-bold text-gold mb-4 flex items-center gap-2">
                        <i class="fas fa-lock"></i> Security
                    </h2>
                    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                        @csrf
                        @method('put')
                        <div>
                            <label class="block text-sm font-medium text-gold-400 mb-1">Current Password</label>
                            <input type="password" name="current_password" required
                                class="w-full px-4 py-2 border border-gold/30 rounded-lg bg-cosmic-void/50 text-white focus:outline-none focus:ring-2 focus:ring-gold">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gold-400 mb-1">New Password</label>
                            <input type="password" name="password" required
                                class="w-full px-4 py-2 border border-gold/30 rounded-lg bg-cosmic-void/50 text-white focus:outline-none focus:ring-2 focus:ring-gold">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gold-400 mb-1">Confirm New Password</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full px-4 py-2 border border-gold/30 rounded-lg bg-cosmic-void/50 text-white focus:outline-none focus:ring-2 focus:ring-gold">
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="btn-golden px-6 py-2">
                                <i class="fas fa-key mr-2"></i> Update Password
                            </button>
                        </div>
                        @if (session('status') === 'password-updated')
                            <p class="text-green-400 text-sm text-center">✅ Password updated!</p>
                        @endif
                    </form>
                </div>

                <!-- Bank Accounts -->
                <div class="card-golden p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gold flex items-center gap-2">
                            <i class="fas fa-university"></i> Bank Accounts
                        </h2>
                        <button @click="openBankModal" class="btn-golden text-sm py-1 px-3">
                            <i class="fas fa-plus mr-1"></i> Add Account
                        </button>
                    </div>
                    @if($bankAccounts->count())
                        <div class="space-y-3">
                            @foreach($bankAccounts as $account)
                            <div class="bg-cosmic-deep/30 rounded-lg p-4 border border-gold/20 flex justify-between items-center">
                                <div>
                                    <p class="font-semibold text-gold">{{ $account->bank_name }}</p>
                                    <p class="text-sm text-ivory/70">{{ $account->account_name }}</p>
                                    <p class="text-xs text-ivory/50">Account: {{ $account->account_number }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <button @click="editBankAccount({{ $account->id }}, '{{ $account->bank_name }}', '{{ $account->account_name }}', '{{ $account->account_number }}')" class="text-gold-400 hover:text-gold">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('profile.bank-account.delete', $account) }}" onsubmit="return confirm('Delete this bank account?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-ivory/50 py-4">No bank accounts added yet.</p>
                    @endif
                </div>

                <!-- Notification Preferences -->
                <div class="card-golden p-6">
                    <h2 class="text-xl font-bold text-gold mb-4 flex items-center gap-2">
                        <i class="fas fa-bell"></i> Notification Preferences
                    </h2>
                    <form method="POST" action="{{ route('profile.notifications.update') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-md font-semibold text-gold-400 mb-2">Email</h3>
                                <label class="flex items-center justify-between p-2">
                                    <span>Deposit Confirmation</span>
                                    <input type="checkbox" name="email_deposit" value="1" {{ $notificationPreferences['email_deposit'] ? 'checked' : '' }} class="toggle-checkbox">
                                </label>
                                <label class="flex items-center justify-between p-2">
                                    <span>Investment Updates</span>
                                    <input type="checkbox" name="email_investment" value="1" {{ $notificationPreferences['email_investment'] ? 'checked' : '' }} class="toggle-checkbox">
                                </label>
                                <label class="flex items-center justify-between p-2">
                                    <span>Withdrawal Status</span>
                                    <input type="checkbox" name="email_withdrawal" value="1" {{ $notificationPreferences['email_withdrawal'] ? 'checked' : '' }} class="toggle-checkbox">
                                </label>
                                <label class="flex items-center justify-between p-2">
                                    <span>Trading Alerts</span>
                                    <input type="checkbox" name="email_trading" value="1" {{ $notificationPreferences['email_trading'] ? 'checked' : '' }} class="toggle-checkbox">
                                </label>
                            </div>
                            <div>
                                <h3 class="text-md font-semibold text-gold-400 mb-2">In‑App</h3>
                                <label class="flex items-center justify-between p-2">
                                    <span>Deposit</span>
                                    <input type="checkbox" name="database_deposit" value="1" {{ $notificationPreferences['database_deposit'] ? 'checked' : '' }} class="toggle-checkbox">
                                </label>
                                <label class="flex items-center justify-between p-2">
                                    <span>Investment</span>
                                    <input type="checkbox" name="database_investment" value="1" {{ $notificationPreferences['database_investment'] ? 'checked' : '' }} class="toggle-checkbox">
                                </label>
                                <label class="flex items-center justify-between p-2">
                                    <span>Withdrawal</span>
                                    <input type="checkbox" name="database_withdrawal" value="1" {{ $notificationPreferences['database_withdrawal'] ? 'checked' : '' }} class="toggle-checkbox">
                                </label>
                                <label class="flex items-center justify-between p-2">
                                    <span>Trading</span>
                                    <input type="checkbox" name="database_trading" value="1" {{ $notificationPreferences['database_trading'] ? 'checked' : '' }} class="toggle-checkbox">
                                </label>
                            </div>
                            <div class="md:col-span-2">
                                <h3 class="text-md font-semibold text-gold-400 mb-2">SMS</h3>
                                <label class="flex items-center justify-between p-2">
                                    <span>Deposit Confirmation</span>
                                    <input type="checkbox" name="sms_deposit" value="1" {{ $notificationPreferences['sms_deposit'] ? 'checked' : '' }} class="toggle-checkbox">
                                </label>
                                <label class="flex items-center justify-between p-2">
                                    <span>Withdrawal Status</span>
                                    <input type="checkbox" name="sms_withdrawal" value="1" {{ $notificationPreferences['sms_withdrawal'] ? 'checked' : '' }} class="toggle-checkbox">
                                </label>
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button type="submit" class="btn-golden px-6 py-2">
                                <i class="fas fa-save mr-2"></i> Save Preferences
                            </button>
                        </div>
                        @if (session('status') === 'preferences-updated')
                            <p class="text-green-400 text-sm text-center mt-2">✅ Preferences updated!</p>
                        @endif
                    </form>
                </div>

                <!-- Danger Zone -->
                <div class="card-golden p-6 border-red-500/30">
                    <h2 class="text-xl font-bold text-red-400 mb-4 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i> Danger Zone
                    </h2>
                    <p class="text-sm text-ivory/70 mb-4">Once you delete your account, all data will be permanently removed.</p>
                    <button @click="openDeleteModal" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                        <i class="fas fa-trash-alt mr-2"></i> Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/80" @click.away="showDeleteModal = false">
        <div class="bg-cosmic-deep rounded-2xl p-6 max-w-md w-full mx-4 border border-red-500/30">
            <h3 class="text-xl font-bold text-red-400 mb-4">Delete Account</h3>
            <p class="text-ivory/70 mb-4">Are you sure you want to delete your account? This action cannot be undone.</p>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gold-400 mb-1">Enter your password to confirm</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2 border border-red-500/30 rounded-lg bg-cosmic-void/50 text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2 border border-gold/30 rounded-lg text-gold-400 hover:bg-gold/10 transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition">
                        Confirm Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bank Account Modal -->
    <div x-show="showBankModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/80" @click.away="showBankModal = false">
        <div class="bg-cosmic-deep rounded-2xl p-6 max-w-md w-full mx-4 border border-gold/30">
            <h3 class="text-xl font-bold text-gold mb-4" x-text="bankModalTitle"></h3>
            <form :action="bankFormAction" method="POST">
                @csrf
                <input type="hidden" name="_method" x-model="bankMethod">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gold-400 mb-1">Bank Name</label>
                        <input type="text" name="bank_name" x-model="bankName" required
                            class="w-full px-4 py-2 border border-gold/30 rounded-lg bg-cosmic-void/50 text-white focus:outline-none focus:ring-2 focus:ring-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gold-400 mb-1">Account Name</label>
                        <input type="text" name="account_name" x-model="accountName" required
                            class="w-full px-4 py-2 border border-gold/30 rounded-lg bg-cosmic-void/50 text-white focus:outline-none focus:ring-2 focus:ring-gold">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gold-400 mb-1">Account Number</label>
                        <input type="text" name="account_number" x-model="accountNumber" required
                            class="w-full px-4 py-2 border border-gold/30 rounded-lg bg-cosmic-void/50 text-white focus:outline-none focus:ring-2 focus:ring-gold">
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" @click="showBankModal = false" class="flex-1 px-4 py-2 border border-gold/30 rounded-lg text-gold-400 hover:bg-gold/10 transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 btn-golden">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function profileManager() {
    return {
        showDeleteModal: false,
        showBankModal: false,
        bankModalTitle: 'Add Bank Account',
        bankFormAction: '{{ route("profile.bank-account.add") }}',
        bankMethod: 'POST',
        bankId: null,
        bankName: '',
        accountName: '',
        accountNumber: '',

        init() {
            // Auto-hide flash messages
            setTimeout(() => {
                const flashes = document.querySelectorAll('.text-green-400, .text-red-400');
                flashes.forEach(el => el.style.opacity = '0');
            }, 3000);
        },

        triggerAvatarUpload() {
            document.getElementById('avatar-input').click();
        },

        uploadAvatar(event) {
            const file = event.target.files[0];
            if (file) {
                document.getElementById('avatar-form').submit();
            }
        },

        openDeleteModal() {
            this.showDeleteModal = true;
        },

        openBankModal() {
            this.bankModalTitle = 'Add Bank Account';
            this.bankFormAction = '{{ route("profile.bank-account.add") }}';
            this.bankMethod = 'POST';
            this.bankId = null;
            this.bankName = '';
            this.accountName = '';
            this.accountNumber = '';
            this.showBankModal = true;
        },

        editBankAccount(id, bankName, accountName, accountNumber) {
            this.bankModalTitle = 'Edit Bank Account';
            this.bankFormAction = `/profile/bank-account/${id}`;
            this.bankMethod = 'PUT';
            this.bankId = id;
            this.bankName = bankName;
            this.accountName = accountName;
            this.accountNumber = accountNumber;
            this.showBankModal = true;
        }
    }
}

function copyReferralCode() {
    const link = "{{ url('/refer/' . (auth()->user()->referral_code ?? '')) }}";
    navigator.clipboard.writeText(link);
    alert('Referral link copied!');
}
</script>
@endsection
