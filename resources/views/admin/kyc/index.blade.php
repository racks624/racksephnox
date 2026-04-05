@extends('admin.layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-6">Pending KYC Documents</h2>

@if($documents->count())
<table class="min-w-full bg-white dark:bg-gray-800 rounded-lg">
    <thead class="bg-gray-100 dark:bg-gray-700">
        <tr>
            <th class="px-6 py-3 text-left">User</th>
            <th class="px-6 py-3 text-left">Document Type</th>
            <th class="px-6 py-3 text-left">Uploaded</th>
            <th class="px-6 py-3 text-left">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($documents as $doc)
        <tr class="border-t">
            <td class="px-6 py-4">{{ $doc->user->name }} ({{ $doc->user->email }})</td>
            <td class="px-6 py-4">{{ str_replace('_', ' ', ucfirst($doc->document_type)) }}</td>
            <td class="px-6 py-4">{{ $doc->created_at->diffForHumans() }}</td>
            <td class="px-6 py-4">
                <a href="{{ route('admin.kyc.show', $doc) }}" class="text-blue-600 hover:underline mr-2">View</a>
                <form method="POST" action="{{ route('admin.kyc.approve', $doc) }}" class="inline">
                    @csrf
                    <button type="submit" class="text-green-600 hover:underline mr-2">Approve</button>
                </form>
                <form method="POST" action="{{ route('admin.kyc.reject', $doc) }}" class="inline" onsubmit="return promptReason(event)">
                    @csrf
                    <input type="hidden" name="reason" id="reason">
                    <button type="submit" class="text-red-600 hover:underline">Reject</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
function promptReason(event) {
    event.preventDefault();
    const reason = prompt('Enter rejection reason:');
    if (reason) {
        document.getElementById('reason').value = reason;
        event.target.submit();
    }
    return false;
}
</script>
@else
<p>No pending KYC documents.</p>
@endif
@endsection
