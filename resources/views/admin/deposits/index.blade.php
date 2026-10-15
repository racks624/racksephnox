@extends('admin.layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-center flex-wrap gap-3">
        <h1 class="text-2xl font-bold text-gold">💳 Deposit Verification Center</h1>
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search code / phone / ref"
                   class="input-golden text-sm">
            <select name="status" class="input-golden text-sm" onchange="this.form.submit()">
                <option value="">Pending</option>
                <option value="verified" @selected(request('status') === 'verified')>Verified</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                <option value="all" @selected(request('status') === 'all')>All</option>
            </select>
            <button class="btn-golden text-sm px-4 py-2">Search</button>
        </form>
    </div>

    {{-- ═══ Stats ═══ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="admin-card p-4">
            <p class="text-xs text-gold-400 uppercase">Pending</p>
            <p class="text-3xl font-bold text-yellow-400">{{ $stats['pending_verification'] }}</p>
            <p class="text-xs text-gold-400/70">KES {{ number_format($stats['pending_amount'], 0) }}</p>
        </div>
        <div class="admin-card p-4">
            <p class="text-xs text-gold-400 uppercase">Verified Today</p>
            <p class="text-3xl font-bold text-green-400">{{ $stats['verified_today'] }}</p>
            <p class="text-xs text-gold-400/70">KES {{ number_format($stats['verified_amount_today'], 0) }}</p>
        </div>
        <div class="admin-card p-4">
            <p class="text-xs text-gold-400 uppercase">Rejected Today</p>
            <p class="text-3xl font-bold text-red-400">{{ $stats['rejected_today'] }}</p>
        </div>
        <div class="admin-card p-4">
            <p class="text-xs text-gold-400 uppercase">Total Rejections</p>
            <p class="text-3xl font-bold text-gold-400">{{ $stats['duplicate_attempts'] }}</p>
        </div>
    </div>

    {{-- ═══ Statement upload ═══ --}}
    <div class="admin-card p-5">
        <h3 class="text-lg font-bold text-gold mb-3">📄 Upload M-Pesa Statement (Auto-Match)</h3>
        <p class="text-xs text-ivory/60 mb-3">
            Upload the CSV export from your M-Pesa business account. The system will auto-match codes against pending deposits.
        </p>
        <form method="POST" action="{{ route('admin.deposits.upload-statement') }}"
              enctype="multipart/form-data" class="flex gap-3 flex-wrap">
            @csrf
            <input type="file" name="statement" accept=".csv,.txt"
                   class="input-golden flex-1" required>
            <button class="btn-golden px-6">Upload & Match</button>
        </form>

        @if($recentStatements->count())
            <div class="mt-4 pt-4 border-t border-gold/20">
                <p class="text-xs text-gold-400 uppercase mb-2">Recent Statements</p>
                <div class="space-y-2">
                    @foreach($recentStatements as $s)
                        <div class="flex justify-between text-sm border-b border-gold/10 py-1">
                            <span class="text-ivory/80">{{ $s->file_name }}</span>
                            <span class="text-gold-400 text-xs">
                                {{ $s->matched_rows }}/{{ $s->total_rows }} matched · {{ $s->match_rate }}%
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- ═══ Deposits table ═══ --}}
    <div class="admin-card p-5 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-gold/30 text-gold-400 text-xs uppercase">
                <tr>
                    <th class="text-left p-2">Submitted</th>
                    <th class="text-left p-2">User</th>
                    <th class="text-left p-2">Amount</th>
                    <th class="text-left p-2">Transaction Code</th>
                    <th class="text-left p-2">Phone</th>
                    <th class="text-left p-2">Match</th>
                    <th class="text-left p-2">Status</th>
                    <th class="text-right p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deposits as $d)
                    <tr class="border-b border-gold/10 hover:bg-gold/5">
                        <td class="p-2 text-ivory/70 text-xs">
                            {{ $d->created_at->format('M d, H:i') }}
                        </td>
                        <td class="p-2">
                            <p class="text-ivory">{{ $d->user->name ?? '—' }}</p>
                            <p class="text-xs text-gold-400/60">{{ $d->user->email ?? '' }}</p>
                        </td>
                        <td class="p-2 text-gold font-bold">KES {{ number_format($d->amount, 2) }}</td>
                        <td class="p-2 font-mono text-xs">
                            <button onclick="copyText('{{ $d->transaction_code }}')"
                                    class="hover:text-gold transition" title="Copy">
                                {{ $d->transaction_code }}
                            </button>
                        </td>
                        <td class="p-2 font-mono text-xs text-ivory/70">{{ $d->phone }}</td>
                        <td class="p-2 text-xs">
                            @if($d->match_confidence > 0)
                                <span class="{{ $d->match_confidence >= 90 ? 'text-green-400' : 'text-yellow-400' }}">
                                    {{ $d->confidence_label }} ({{ $d->match_confidence }}%)
                                </span>
                            @else
                                <span class="text-ivory/40">Manual</span>
                            @endif
                        </td>
                        <td class="p-2">
                            <span class="uppercase text-xs {{ $d->status_color }}">
                                {{ $d->verification_status }}
                            </span>
                        </td>
                        <td class="p-2 text-right whitespace-nowrap">
                            @if($d->is_awaiting_verification)
                                <form method="POST" action="{{ route('admin.deposits.verify', $d) }}"
                                      class="inline">
                                    @csrf
                                    <button class="text-green-400 hover:text-green-300 text-xs px-2"
                                            onclick="return confirm('Verify and credit KES {{ number_format($d->amount, 2) }}?')">
                                        ✓ Verify
                                    </button>
                                </form>
                                <button onclick="openReject({{ $d->id }})"
                                        class="text-red-400 hover:text-red-300 text-xs px-2">
                                    ✕ Reject
                                </button>
                            @else
                                <span class="text-ivory/30 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-ivory/50">
                            No deposits awaiting verification.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">{{ $deposits->links() }}</div>
    </div>

</div>

{{-- Reject modal --}}
<div id="rejectModal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center">
    <div class="bg-cosmic-deep rounded-2xl p-6 max-w-md w-full border border-gold/30">
        <h3 class="text-lg font-bold text-gold mb-3">Reject Deposit</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <textarea name="reason" class="input-golden w-full mb-3" rows="3"
                      placeholder="Reason for rejection..." required></textarea>
            <div class="flex gap-3">
                <button type="button" onclick="closeReject()" class="btn-outline-silver flex-1">Cancel</button>
                <button type="submit" class="btn-golden flex-1 bg-red-500/80">Reject</button>
            </div>
        </form>
    </div>
</div>

<script>
function copyText(text) {
    navigator.clipboard.writeText(text)
        .then(() => window.showToast?.('Copied!', 'success'))
        .catch(() => {});
}
function openReject(id) {
    const modal = document.getElementById('rejectModal');
    document.getElementById('rejectForm').action = `/admin/deposits/${id}/reject`;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeReject() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection
