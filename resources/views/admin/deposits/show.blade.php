@extends('admin.layouts.app')

@section('content')
<div class="space-y-6 max-w-4xl">

    <a href="{{ route('admin.deposits.index') }}" class="text-gold-400 text-sm">← Back to Queue</a>

    <div class="admin-card p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gold">Verify Deposit</h1>
                <p class="text-xs text-ivory/60">Reference: {{ $deposit->reference }}</p>
            </div>
            <span class="text-sm uppercase font-bold {{ $deposit->status_color }}">{{ $deposit->status }}</span>
        </div>

        {{-- Deposit Details --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-gold/5 rounded-lg p-3">
                <p class="text-xs text-gold-400 uppercase">User</p>
                <p class="text-ivory font-bold">{{ $deposit->user?->name }}</p>
                <p class="text-xs text-gold-400/60">{{ $deposit->user?->email }}</p>
            </div>
            <div class="bg-gold/5 rounded-lg p-3">
                <p class="text-xs text-gold-400 uppercase">Amount Claimed</p>
                <p class="text-2xl font-bold text-gold">KES {{ number_format($deposit->amount, 2) }}</p>
            </div>
            <div class="bg-gold/5 rounded-lg p-3">
                <p class="text-xs text-gold-400 uppercase">Transaction Code</p>
                <p class="text-2xl font-mono font-bold text-gold tracking-wider">{{ $deposit->transaction_code }}</p>
            </div>
            <div class="bg-gold/5 rounded-lg p-3">
                <p class="text-xs text-gold-400 uppercase">Phone</p>
                <p class="text-ivory font-mono">{{ $deposit->phone }}</p>
            </div>
            <div class="bg-gold/5 rounded-lg p-3">
                <p class="text-xs text-gold-400 uppercase">Payment Date</p>
                <p class="text-ivory">{{ optional($deposit->payment_date)->format('M d, Y') }}</p>
            </div>
            <div class="bg-gold/5 rounded-lg p-3">
                <p class="text-xs text-gold-400 uppercase">Paybill / Account</p>
                <p class="text-ivory font-mono text-sm">{{ $deposit->paybill }} / {{ $deposit->account_number }}</p>
            </div>
        </div>

        {{-- Verification Panel --}}
        <div id="verifyPanel" class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-4 mb-6">
            <div class="flex justify-between items-center mb-2">
                <p class="text-sm font-bold text-blue-300">🔍 Verification Check</p>
                <button onclick="runVerification()" id="verifyBtn" class="btn-outline-silver text-xs px-3 py-1">
                    Run Check
                </button>
            </div>
            <div id="verifyResult" class="text-xs text-ivory/70">
                Click <strong>Run Check</strong> to validate format and (if configured) query Safaricom Daraja.
            </div>
        </div>

        {{-- Manual Confirmation Steps --}}
        <div class="bg-gold/5 rounded-xl p-4 mb-6">
            <p class="text-sm font-bold text-gold mb-2">📋 Manual Confirmation Steps</p>
            <ol class="text-xs text-ivory/70 space-y-1 list-decimal list-inside">
                <li>Log into M-Pesa Business portal (or your M-Pesa app).</li>
                <li>Open <strong>Transaction Statement</strong> for Paybill <strong>{{ $deposit->paybill }}</strong>.</li>
                <li>Search for code <strong class="text-gold font-mono">{{ $deposit->transaction_code }}</strong>.</li>
                <li>Confirm: amount = <strong>KES {{ number_format($deposit->amount, 2) }}</strong>,
                    account = <strong>{{ $deposit->account_number }}</strong>,
                    phone = <strong>{{ $deposit->phone }}</strong>.</li>
                <li>If everything matches, click <strong>Approve</strong> below.</li>
            </ol>
        </div>

        {{-- Approve / Reject --}}
        @if($deposit->status !== 'approved')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <form method="POST" action="{{ route('admin.deposits.approve', $deposit) }}">
                    @csrf
                    <input type="text" name="admin_note" class="input-golden w-full text-sm mb-2"
                           placeholder="Admin note (optional)">
                    <button class="btn-golden w-full py-3 font-bold">✅ Approve & Credit Wallet</button>
                </form>

                <form method="POST" action="{{ route('admin.deposits.reject', $deposit) }}"
                      onsubmit="return confirm('Reject this deposit?')">
                    @csrf
                    <input type="text" name="reason" class="input-golden w-full text-sm mb-2"
                           placeholder="Rejection reason (required)" required>
                    <button class="btn-outline-silver w-full py-3 font-bold text-red-400">❌ Reject</button>
                </form>
            </div>
        @endif
    </div>
</div>

<script>
async function runVerification() {
    const btn = document.getElementById('verifyBtn');
    const out = document.getElementById('verifyResult');
    btn.disabled = true;
    btn.textContent = 'Checking…';
    out.innerHTML = 'Running verification…';

    try {
        const res = await fetch('{{ route('admin.deposits.verify', $deposit) }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
        });
        const data = await res.json();
        const v = data.verification;

        const color = v.verified ? 'text-green-400' : 'text-yellow-400';
        out.innerHTML = `
            <p class="${color} font-bold mb-1">
                ${v.verified ? '✅ Auto-verified' : '⚠️ ' + v.reason}
            </p>
            <p>Method: <strong>${v.method}</strong></p>
            ${v.receipt ? '<pre class="text-[10px] mt-2 bg-black/40 p-2 rounded">' + JSON.stringify(v.receipt, null, 2) + '</pre>' : ''}
            <p class="mt-2 text-ivory/60">Now verify manually via M-Pesa portal, then approve or reject.</p>
        `;
    } catch (e) {
        out.innerHTML = '<p class="text-red-400">Verification failed: ' + e.message + '</p>';
    } finally {
        btn.disabled = false;
        btn.textContent = 'Run Check';
    }
}
</script>
@endsection
