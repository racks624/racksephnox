@extends('layouts.app')

@section('title', 'Deposit Funds')

@section('content')
<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold golden-title">🏦 Banking Center</h1>
            <p class="text-gold-400 mt-2 text-sm">Deposit via M-Pesa · Instant confirmation</p>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-500/20 border border-green-500/40 rounded-xl p-3 text-green-300 text-sm">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 bg-red-500/20 border border-red-500/40 rounded-xl p-3 text-red-300 text-sm">
                <i class="fas fa-exclamation-circle mr-1"></i> {{ $errors->first() }}
            </div>
        @endif

        {{-- ═══ STEP 1 — Paybill details ═══ --}}
        <div class="card-golden p-6 mb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-gold/20 flex items-center justify-center text-gold font-bold">1</div>
                <h2 class="text-xl font-bold text-gold">Send Money via M-Pesa</h2>
            </div>

            <p class="text-ivory/70 text-sm mb-4">
                Open <strong>M-Pesa</strong> on your phone → <strong>Lipa na M-Pesa</strong> → <strong>Pay Bill</strong>, then enter:
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="bg-gold/10 rounded-xl p-4 border border-gold/30">
                    <p class="text-xs text-gold-400 uppercase tracking-widest mb-1">Paybill Number</p>
                    <p class="text-2xl md:text-3xl font-bold text-gold font-mono tracking-wider" id="paybillValue">{{ $paybill }}</p>
                    <button onclick="copyText('{{ $paybill }}')"
                            class="mt-2 text-xs text-gold-400 hover:text-gold transition">
                        <i class="fas fa-copy mr-1"></i> Copy Paybill
                    </button>
                </div>
                <div class="bg-gold/10 rounded-xl p-4 border border-gold/30">
                    <p class="text-xs text-gold-400 uppercase tracking-widest mb-1">Account Number</p>
                    <p class="text-2xl md:text-3xl font-bold text-gold font-mono tracking-wider" id="accountValue">{{ $account }}</p>
                    <button onclick="copyText('{{ $account }}')"
                            class="mt-2 text-xs text-gold-400 hover:text-gold transition">
                        <i class="fas fa-copy mr-1"></i> Copy Account
                    </button>
                </div>
            </div>

            <div class="bg-gold/5 rounded-xl p-4 border border-gold/20">
                <p class="text-xs text-gold-400 uppercase tracking-widest mb-2">Steps</p>
                <ol class="text-sm text-ivory/80 space-y-1 list-decimal list-inside">
                    <li>Select <strong>Pay Bill</strong></li>
                    <li>Business Number: <strong class="text-gold">{{ $paybill }}</strong></li>
                    <li>Account Number: <strong class="text-gold">{{ $account }}</strong></li>
                    <li>Enter your amount</li>
                    <li>Enter M-Pesa PIN and confirm</li>
                    <li>You'll receive an SMS with a <strong>transaction code</strong> (e.g., SFE4A8B2C1)</li>
                </ol>
            </div>
        </div>

        {{-- ═══ STEP 2 — Submit code ═══ --}}
        <div class="card-golden p-6 mb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-gold/20 flex items-center justify-center text-gold font-bold">2</div>
                <h2 class="text-xl font-bold text-gold">Confirm Your Deposit</h2>
            </div>

            <p class="text-ivory/70 text-sm mb-4">
                Enter the transaction code from your M-Pesa SMS below. Our system will verify it against the Racksephnox Paybill account.
            </p>

            <form method="POST" action="{{ route('deposit.submit') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="text-xs text-gold-400 block mb-1">Amount Sent (KES)</label>
                    <input type="number" name="amount" step="1" min="10" max="1000000"
                           value="{{ old('amount') }}"
                           class="input-golden w-full text-lg" placeholder="e.g. 500" required>
                </div>

                <div>
                    <label class="text-xs text-gold-400 block mb-1">M-Pesa Transaction Code</label>
                    <input type="text" name="transaction_code"
                           value="{{ old('transaction_code') }}"
                           maxlength="16" pattern="[A-Za-z][A-Za-z0-9]{7,15}"
                           class="input-golden w-full font-mono text-lg uppercase tracking-wider"
                           placeholder="e.g. SFE4A8B2C1"
                           style="text-transform: uppercase;"
                           required>
                    <p class="text-xs text-gold-400/60 mt-1">
                        Found in the M-Pesa confirmation SMS you just received.
                    </p>
                </div>

                <div>
                    <label class="text-xs text-gold-400 block mb-1">Phone Used to Send</label>
                    <input type="tel" name="phone" value="{{ old('phone', Auth::user()->phone) }}"
                           class="input-golden w-full font-mono" placeholder="254712345678" required>
                </div>

                <div>
                    <label class="text-xs text-gold-400 block mb-1">Notes (optional)</label>
                    <textarea name="notes" class="input-golden w-full" rows="2"
                              placeholder="Anything we should know?">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn-golden w-full py-3 font-bold text-lg">
                    💳 Submit Deposit for Verification
                </button>
            </form>
        </div>

        {{-- ═══ Recent deposits ═══ --}}
        @if($recent->count())
            <div class="card-golden p-5">
                <h3 class="text-lg font-bold text-gold mb-4">Your Recent Deposits</h3>
                <div class="space-y-2">
                    @foreach($recent as $d)
                        <a href="{{ route('deposit.status', $d->id) }}"
                           class="flex justify-between items-center p-3 bg-gold/5 rounded-lg hover:bg-gold/10 transition">
                            <div>
                                <p class="text-sm font-bold text-gold">KES {{ number_format($d->amount, 2) }}</p>
                                <p class="text-xs text-gold-400/70 font-mono">{{ $d->transaction_code ?? '—' }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs uppercase {{ $d->status_color }}">
                                    {{ ucfirst($d->verification_status) }}
                                </span>
                                <p class="text-xs text-ivory/50 mt-1">
                                    {{ $d->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="text-center mt-10 pt-6 border-t border-gold/20">
            <p class="text-xs text-gold-400/60">Φ = 1.618 · 888 Hz · Secure M-Pesa Integration</p>
        </div>

    </div>
</div>

<script>
function copyText(text) {
    navigator.clipboard.writeText(text)
        .then(() => window.showToast?.('Copied!', 'success') || alert('Copied: ' + text))
        .catch(() => alert('Copy failed'));
}
</script>
@endsection
