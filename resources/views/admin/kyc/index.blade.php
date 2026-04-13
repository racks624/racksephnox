@extends('admin.layouts.app')

@section('content')
<div class="admin-card p-6">
    <h2 class="text-2xl font-bold text-gold mb-6">📄 Pending KYC Documents</h2>

    @if($documents->count())
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-gold/30">
                    <tr class="text-gold-400 text-left">
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Document Type</th>
                        <th class="px-4 py-3">Uploaded</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gold/20">
                    @foreach($documents as $doc)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-ivory">{{ $doc->user->name }}</div>
                            <div class="text-xs text-ivory/50">{{ $doc->user->email }}</div>
                        </td>
                        <td class="px-4 py-3 text-ivory">{{ str_replace('_', ' ', ucfirst($doc->document_type)) }}</td>
                        <td class="px-4 py-3 text-ivory/70">{{ $doc->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('admin.kyc.show', $doc) }}" class="btn-golden text-sm py-1 px-3">View</a>
                            <form method="POST" action="{{ route('admin.kyc.approve', $doc) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-green-400 hover:text-green-300 text-sm px-2">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.kyc.reject', $doc) }}" class="inline" onsubmit="return promptReason(event, this)">
                                @csrf
                                <input type="hidden" name="reason" class="reason-input">
                                <button type="submit" class="text-red-400 hover:text-red-300 text-sm px-2">Reject</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-center text-ivory/50 py-8">✨ No pending KYC documents.</p>
    @endif
</div>

<script>
function promptReason(event, form) {
    event.preventDefault();
    const reason = prompt('Enter rejection reason:');
    if (reason) {
        form.querySelector('.reason-input').value = reason;
        form.submit();
    }
    return false;
}
</script>
@endsection
