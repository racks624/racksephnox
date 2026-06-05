@extends('admin.layouts.app')

@section('content')
<div class="admin-card p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gold">🔍 KYC Document Review</h2>
        <a href="{{ route('admin.kyc.index') }}" class="btn-golden text-sm py-1 px-3">← Back</a>
    </div>

    <div class="space-y-4">
        <div><strong class="text-gold-400">User:</strong> <span class="text-ivory">{{ $document->user->name }} ({{ $document->user->email }})</span></div>
        <div><strong class="text-gold-400">Document Type:</strong> <span class="text-ivory">{{ str_replace('_', ' ', ucfirst($document->document_type)) }}</span></div>
        <div><strong class="text-gold-400">Uploaded:</strong> <span class="text-ivory/70">{{ $document->created_at->format('Y-m-d H:i') }}</span></div>
        <div><strong class="text-gold-400">File:</strong> <a href="{{ Storage::url($document->document_path) }}" target="_blank" class="text-gold-400 hover:text-gold underline">View Document ↗</a></div>
    </div>

    <div class="mt-6 flex gap-3">
        <form method="POST" action="{{ route('admin.kyc.approve', $document) }}">
            @csrf
            <button type="submit" class="btn-golden">✅ Approve</button>
        </form>
        <form method="POST" action="{{ route('admin.kyc.reject', $document) }}" onsubmit="return confirmRejection(event, this)">
            @csrf
            <input type="text" name="reason" placeholder="Rejection reason" class="px-3 py-2 rounded-lg border border-gold/30 bg-cosmic-void text-ivory" required>
            <button type="submit" class="ml-2 px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-white">❌ Reject</button>
        </form>
    </div>
</div>

<script>
function confirmRejection(event, form) {
    const reason = form.querySelector('input[name="reason"]').value.trim();
    if (!reason) {
        alert('Please provide a rejection reason.');
        event.preventDefault();
        return false;
    }
    return confirm(`Are you sure you want to reject this document?\nReason: ${reason}`);
}
</script>
@endsection
