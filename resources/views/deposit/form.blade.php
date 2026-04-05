@extends('layouts.app')

@section('content')
<div x-data="depositManager()" x-init="init()" class="py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <h1 class="text-2xl font-bold text-gold mb-4">💰 Divine Deposit Portal</h1>
            <p class="text-ivory/70 mb-6">Send money to the following M-Pesa Paybill number and enter the transaction code.</p>

            <!-- Default Paybill Number -->
            <div class="bg-gradient-to-r from-gold-600/20 to-gold-400/10 rounded-lg p-4 mb-6 text-center border border-gold/30">
                <p class="text-ivory/60 text-sm">📞 M-Pesa Paybill Number</p>
                <p class="text-2xl font-bold text-gold" id="selectedNumber">{{ $selectedNumber }}</p>
                <button onclick="copyNumber()" class="mt-2 btn-golden text-sm">📋 Copy Number</button>
            </div>

            <!-- Deposit Limits Info -->
            <div class="bg-gold/5 rounded-lg p-3 mb-4 text-center">
                <p class="text-xs text-ivory/50">Min: KES {{ number_format($minDeposit) }} | Max: KES {{ number_format($maxDeposit) }}</p>
            </div>

            <form method="POST" action="{{ route('deposit.submit') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm text-gold-400 mb-1">Amount (KES)</label>
                    <input type="number" name="amount" step="0.01" min="{{ $minDeposit }}" max="{{ $maxDeposit }}" class="input-golden w-full" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gold-400 mb-1">M-Pesa Transaction Code</label>
                    <input type="text" name="transaction_reference" placeholder="e.g., QWER1234" class="input-golden w-full" required>
                </div>
                <input type="hidden" name="phone_number" value="{{ $selectedNumber }}">
                <button type="submit" class="btn-golden w-full">Submit Deposit Request</button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-xs text-ivory/50">🎁 First deposit bonus: KES 40 | Consecutive deposit bonus: KES 20</p>
            </div>
        </div>
    </div>
</div>

<script>
function copyNumber() {
    const number = document.getElementById('selectedNumber').innerText;
    navigator.clipboard.writeText(number);
    alert('Number copied to clipboard!');
}
function depositManager() { return { init() {} } }
</script>
@endsection
