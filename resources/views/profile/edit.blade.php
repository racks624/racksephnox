@extends('layouts.app')

@section('content')
<div x-data="profileManager()" x-init="init()" class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold golden-title">✨ Divine Profile</h1>
            <p class="text-gold-400 mt-2">Manage your sacred account information</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="card-golden p-6 text-center sticky top-20">
                    <div class="relative inline-block">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover mx-auto border-2 border-gold">
                        @else
                            <div class="w-24 h-24 rounded-full bg-gradient-to-r from-gold-400 to-gold-600 flex items-center justify-center mx-auto">
                                <i class="fas fa-user text-4xl text-white"></i>
                            </div>
                        @endif
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
                    <div class="mt-2">
                        {!! $kycBadge !!}
                    </div>
                    
                    <div class="mt-6 pt-4 border-t border-gold/20">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-ivory/60">Member since</span>
                                <span class="text-gold">{{ $stats['member_since'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-ivory/60">Referral Code</span>
                                <span class="text-gold font-mono">{{ $user->referral_code ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button onclick="copyReferralLink()" class="btn-golden w-full text-sm py-2">
                            <i class="fas fa-link mr-1"></i> Copy Referral Link
                        </button>
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
                            <p class="text-xs text-ivory/50 mt-1">Minimum 8 characters, 1 uppercase, 1 number</p>
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
                            <p class="text-green-400 text-sm text-center">✅ Password updated successfully!</p>
                        @endif
                    </form>
                </div>

                <!-- Statistics -->
                <div class="card-golden p-6">
                    <h2 class="text-xl font-bold text-gold mb-4 flex items-center gap-2">
                        <i class="fas fa-chart-line"></i> Your Statistics
                    </h2>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="bg-gold/10 rounded-lg p-3 text-center">
                            <p class="text-xs text-ivory/50">Total Invested</p>
                            <p class="text-xl font-bold text-gold">KES {{ number_format($stats['total_invested'], 2) }}</p>
                        </div>
                        <div class="bg-gold/10 rounded-lg p-3 text-center">
                            <p class="text-xs text-ivory/50">Machine Invested</p>
                            <p class="text-xl font-bold text-gold">KES {{ number_format($stats['total_machine_invested'], 2) }}</p>
                        </div>
                        <div class="bg-gold/10 rounded-lg p-3 text-center">
                            <p class="text-xs text-ivory/50">Total Referrals</p>
                            <p class="text-xl font-bold text-gold">{{ $stats['total_referrals'] }}</p>
                        </div>
                        <div class="bg-gold/10 rounded-lg p-3 text-center">
                            <p class="text-xs text-ivory/50">Referral Bonus</p>
                            <p class="text-xl font-bold text-green-400">KES {{ number_format($stats['total_bonus'], 2) }}</p>
                        </div>
                        <div class="bg-gold/10 rounded-lg p-3 text-center">
                            <p class="text-xs text-ivory/50">Total Interest</p>
                            <p class="text-xl font-bold text-green-400">KES {{ number_format($stats['total_interest'], 2) }}</p>
                        </div>
                        <div class="bg-gold/10 rounded-lg p-3 text-center">
                            <p class="text-xs text-ivory/50">Member Since</p>
                            <p class="text-xl font-bold text-gold">{{ $stats['member_since'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card-golden p-6">
                    <h2 class="text-xl font-bold text-gold mb-4 flex items-center gap-2">
                        <i class="fas fa-history"></i> Recent Activity
                    </h2>
                    
                    @if($recentActivities->count())
                        <div class="space-y-2">
                            @foreach($recentActivities as $activity)
                            <div class="flex justify-between items-center p-3 bg-gold/5 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">
                                        @if($activity->type == 'deposit') 💸
                                        @elseif($activity->type == 'withdrawal') 💳
                                        @elseif($activity->type == 'interest') 📈
                                        @elseif($activity->type == 'machine_investment') 🤖
                                        @else 🔔
                                        @endif
                                    </span>
                                    <div>
                                        <p class="text-sm text-ivory">{{ $activity->description ?? ucfirst($activity->type) }}</p>
                                        <p class="text-xs text-ivory/50">{{ $activity->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <span class="text-sm font-semibold {{ $activity->amount > 0 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $activity->amount > 0 ? '+' : '' }}{{ number_format($activity->amount, 2) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-ivory/50 py-4">No recent activity</p>
                    @endif
                </div>

                <!-- Danger Zone -->
                <div class="card-golden p-6 border-red-500/30">
                    <h2 class="text-xl font-bold text-red-400 mb-4 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i> Danger Zone
                    </h2>
                    
                    <p class="text-sm text-ivory/70 mb-4">Once you delete your account, all data will be permanently removed.</p>
                    
                    <button type="button" @click="openDeleteModal" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition">
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
</div>

<script>
function profileManager() {
    return {
        showDeleteModal: false,
        
        init() {
            // Auto-hide flash messages
            setTimeout(() => {
                const flash = document.querySelector('.text-green-400');
                if (flash) flash.style.opacity = '0';
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
        }
    }
}

function copyReferralLink() {
    const link = "{{ url('/refer/' . ($user->referral_code ?? '')) }}";
    navigator.clipboard.writeText(link);
    alert('Referral link copied to clipboard!');
}
</script>
@endsection
