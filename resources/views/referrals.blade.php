@extends('layouts.app')

@section('content')
<div x-data="referralsManager()" x-init="init()" class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold golden-title" aria-label="Referral Program">Referral Program</h1>
            <p class="text-gold-400 mt-2">Invite friends and earn {{ config('referral.bonus_rate') }}% of their deposits</p>
        </div>

        <!-- Loading Skeleton -->
        <div x-show="loading" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
                <div class="animate-pulse bg-gold-500/10 rounded-2xl h-40"></div>
                <div class="animate-pulse bg-gold-500/10 rounded-2xl h-40"></div>
                <div class="animate-pulse bg-gold-500/10 rounded-2xl h-40"></div>
            </div>
            <div class="animate-pulse bg-gold-500/10 rounded-2xl h-64 mb-10"></div>
            <div class="animate-pulse bg-gold-500/10 rounded-2xl h-64"></div>
        </div>

        <!-- Actual Content -->
        <div x-show="!loading" x-cloak>
            <!-- Top Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
                <!-- Referral Link Card -->
                <div class="card-golden p-6 lg:col-span-2">
                    <h2 class="text-xl font-bold text-gold mb-4">Your Referral Link</h2>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" id="referralLink" value="{{ url('/refer/' . auth()->user()->referral_code) }}" 
                               class="input-golden flex-1" readonly aria-label="Referral link">
                        <button onclick="copyReferralLink()" class="btn-golden" aria-label="Copy referral link">Copy Link</button>
                    </div>
                    <p class="text-sm text-ivory/50 mt-3">Share this link with friends. When they sign up and deposit, you earn a {{ config('referral.bonus_rate') }}% bonus.</p>
                </div>

                <!-- Stats Cards -->
                <div class="card-golden p-6">
                    <h2 class="text-xl font-bold text-gold mb-4">Your Impact</h2>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-ivory/60">Total Referrals</p>
                            <p class="text-3xl font-bold text-gold">{{ number_format($totalReferrals) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-ivory/60">Total Bonus Earned</p>
                            <p class="text-3xl font-bold text-gold">KES {{ number_format($totalBonus, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Stats -->
            <div class="card-golden p-6 mb-10">
                <h2 class="text-xl font-bold text-gold mb-4">Monthly Referrals</h2>
                <div class="overflow-x-auto">
                    <table class="w-full" aria-label="Monthly referrals breakdown">
                        <thead class="border-b border-gold/30">
                            指数
                                <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Month</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">New Referrals</th>
                             </thead>
                        <tbody class="divide-y divide-gold/20">
                            @foreach($monthlyStats as $stat)
                            <tr class="hover:bg-gold/5 transition">
                                <td class="px-4 py-3 text-ivory">{{ $stat->month }} </td>
                                <td class="px-4 py-3 text-gold">{{ $stat->count }} </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Referral List -->
            <div class="card-golden p-6">
                <h2 class="text-xl font-bold text-gold mb-4">Your Referrals</h2>
                @if($referrals->count())
                    <div class="overflow-x-auto">
                        <table class="w-full" aria-label="List of referred users">
                            <thead class="border-b border-gold/30">
                                指数
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Joined</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Status</th>
                                 </thead>
                            <tbody class="divide-y divide-gold/20">
                                @foreach($referrals as $ref)
                                <tr class="hover:bg-gold/5 transition">
                                    <td class="px-4 py-3 text-ivory">{{ $ref->name }} </td>
                                    <td class="px-4 py-3 text-ivory">{{ $ref->email }} </td>
                                    <td class="px-4 py-3 text-ivory">{{ $ref->created_at->format('Y-m-d') }} </td>
                                    <td class="px-4 py-3">
                                        @if($ref->transactions()->where('type', 'deposit')->exists())
                                            <span class="text-green-400">Active</span>
                                        @else
                                            <span class="text-ivory/50">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-ivory/50 py-8">No referrals yet. Share your link to start earning!</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function copyReferralLink() {
    const link = document.getElementById('referralLink');
    link.select();
    document.execCommand('copy');
    alert('Referral link copied to clipboard!');
}

function referralsManager() {
    return {
        loading: true,
        init() {
            // Simulate loading (or fetch data if needed)
            setTimeout(() => {
                this.loading = false;
            }, 300);
        }
    }
}
</script>
@endsection
