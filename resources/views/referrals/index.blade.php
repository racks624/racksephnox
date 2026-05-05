@extends('layouts.app')
@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <h1 class="text-2xl font-bold text-gold mb-4">🤝 Referral Program</h1>
            <div class="bg-gold/10 rounded-lg p-4 mb-6 text-center">
                <p class="text-ivory/60">Your Referral Link</p>
                <code class="text-gold break-all" id="referral-link">{{ url('/register?ref=' . Auth::user()->referral_code) }}</code>
                <button onclick="copyLink()" class="ml-2 btn-golden text-sm">Copy</button>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="text-center"><p class="text-ivory/60">Total Referrals</p><p class="text-3xl font-bold text-gold">{{ $referrals->count() }}</p></div>
                <div class="text-center"><p class="text-ivory/60">Total Bonus Earned</p><p class="text-3xl font-bold text-green-400">KES {{ number_format($totalBonus, 2) }}</p></div>
            </div>
            @if($referrals->count())
            <h2 class="text-xl font-bold text-gold mb-3">Your Referrals</h2>
            <ul class="divide-y divide-gold/20">
                @foreach($referrals as $ref)
                <li class="py-2 flex justify-between"><span>{{ $ref->name }}</span><span class="text-ivory/50">{{ $ref->created_at->format('d M Y') }}</span></li>
                @endforeach
            </ul>
            @endif
            <h2 class="text-xl font-bold text-gold mt-6 mb-3">Bonus History</h2>
            @if($bonuses->count())
            <ul class="divide-y divide-gold/20">@foreach($bonuses as $bonus)<li class="py-2 flex justify-between"><span>{{ $bonus->description }}</span><span class="text-green-400">+KES {{ number_format($bonus->amount,2) }}</span></li>@endforeach</ul>
            @else <p class="text-ivory/50">No bonuses yet.</p> @endif
        </div>
    </div>
</div>
<script>function copyLink(){ navigator.clipboard.writeText(document.getElementById('referral-link').innerText); alert('Referral link copied!'); }</script>
@endsection
