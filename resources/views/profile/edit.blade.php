@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Profile Settings -->
            <div class="lg:col-span-2">
                <div class="card-golden p-6">
                    <h2 class="text-2xl font-bold text-gold mb-4">Profile Settings</h2>
                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="block text-sm font-medium text-gold-400">Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-golden w-full" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gold-400">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-golden w-full" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gold-400">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="input-golden w-full">
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="btn-golden">Save Changes</button>
                        </div>
                    </form>
                </div>

                <div class="card-golden p-6 mt-6">
                    <h2 class="text-2xl font-bold text-gold mb-4">Change Password</h2>
                    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-medium text-gold-400">Current Password</label>
                            <input type="password" name="current_password" class="input-golden w-full" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gold-400">New Password</label>
                            <input type="password" name="password" class="input-golden w-full" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gold-400">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="input-golden w-full" required>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="btn-golden">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Lottery Stats & Achievements -->
            <div class="space-y-6">
                <div class="card-golden p-6">
                    <h3 class="text-xl font-bold text-gold mb-4">🎰 Your Lottery Journey</h3>
                    <div class="grid grid-cols-2 gap-3 text-center">
                        <div><p class="text-ivory/60 text-xs">Total Spins</p><p class="text-2xl font-bold text-gold">{{ $user->lotterySpins->count() }}</p></div>
                        <div><p class="text-ivory/60 text-xs">Total Won</p><p class="text-2xl font-bold text-green-400">KES {{ number_format($user->lotterySpins->sum('win_amount'), 2) }}</p></div>
                        <div><p class="text-ivory/60 text-xs">Mini Jackpots</p><p class="text-2xl font-bold text-pink-400">{{ $user->lotterySpins->where('mini_jackpot_hit', true)->count() }}</p></div>
                        <div><p class="text-ivory/60 text-xs">Super Jackpots</p><p class="text-2xl font-bold text-gold">{{ $user->lotterySpins->where('super_jackpot_hit', true)->count() }}</p></div>
                        <div><p class="text-ivory/60 text-xs">Free Spins Left</p><p class="text-2xl font-bold text-gold">{{ $user->free_spins_available ?? 0 }}</p></div>
                        <div><p class="text-ivory/60 text-xs">Tax Contributed</p><p class="text-2xl font-bold text-yellow-400">KES {{ number_format($user->lotterySpins->sum('tax_contribution'), 2) }}</p></div>
                    </div>
                </div>

                <div class="card-golden p-6">
                    <h3 class="text-xl font-bold text-gold mb-4">🏆 Achievements</h3>
                    @php
                        $achievements = \App\Models\LotteryAchievement::all();
                        $userAchievements = $user->lotteryUserMissions->pluck('mission_id')->toArray();
                    @endphp
                    <div class="space-y-2">
                        @foreach($achievements as $achievement)
                        <div class="flex items-center gap-3 p-2 rounded-lg {{ in_array($achievement->id, $userAchievements) ? 'bg-gold/10 border border-gold/30' : 'bg-black/20' }}">
                            <i class="fas {{ $achievement->icon }} text-2xl text-gold"></i>
                            <div>
                                <p class="font-semibold text-ivory">{{ $achievement->name }}</p>
                                <p class="text-xs text-ivory/60">{{ $achievement->description }}</p>
                            </div>
                            @if(in_array($achievement->id, $userAchievements))
                                <span class="ml-auto text-green-400 text-sm">✅ Unlocked</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
