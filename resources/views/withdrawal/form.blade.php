@extends('layouts.app')

@section('content')
<div x-data="withdrawalManager()" x-init="init()" class="py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <h1 class="text-2xl font-bold text-gold mb-4">💸 Divine Withdrawal Portal</h1>
            <p class="text-ivory/70 mb-6">Withdraw funds from your wallet to your M-Pesa account.</p>

            <!-- Balance Display -->
            <div class="bg-gold/10 rounded-lg p-4 mb-6 text-center">
                <p class="text-ivory/60 text-sm">Available Balance</p>
                <p class="text-2xl font-bold text-gold">KES {{ number_format($user->wallet->balance, 2) }}</p>
            </div>

            <!-- Fee Table Preview -->
            <div class="bg-gold/5 rounded-lg p-3 mb-4">
                <p class="text-xs text-gold-400 text-center">Fee Structure: 26 (530-2500) | 52 (2501-8000) | 107 (8001-16k) | 1008 (16k-32k) | 3500 (32k-64k) | 10000 (64k-132k) | 25000 (132k-500k) | 88000 (500k-1M)</p>
            </div>

            <form method="POST" action="{{ route('withdrawal.submit') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm text-gold-400 mb-1">Amount (KES)</label>
                    <input type="number" name="amount" step="0.01" min="{{ $minWithdrawal }}" max="{{ $maxWithdrawal }}" class="input-golden w-full" required>
                    <p class="text-xs text-ivory/50 mt-1">Min: KES {{ number_format($minWithdrawal) }} | Max: KES {{ number_format($maxWithdrawal) }}</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gold-400 mb-1">M-Pesa Phone Number</label>
                    <input type="tel" name="phone" placeholder="254712345678" class="input-golden w-full" required>
                </div>
                <button type="submit" class="btn-golden w-full">Submit Withdrawal Request</button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-xs text-ivory/50">⏳ Withdrawals are processed within 48 hours.</p>
            </div>
        </div>
    </div>
</div>

<script>
function withdrawalManager() { return { init() {} } }
</script>
@endsection
