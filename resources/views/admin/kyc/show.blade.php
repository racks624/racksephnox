@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">KYC Document</h2>
    <a href="{{ route('admin.kyc.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded">Back</a>
</div>

<div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
    <p><strong>User:</strong> {{ $document->user->name }} ({{ $document->user->email }})</p>
    <p><strong>Document Type:</strong> {{ str_replace('_', ' ', ucfirst($document->document_type)) }}</p>
    <p><strong>Uploaded:</strong> {{ $document->created_at->format('Y-m-d H:i') }}</p>
    <p><strong>File:</strong> <a href="{{ Storage::url($document->document_path) }}" target="_blank" class="text-blue-600 underline">View Document</a></p>
    <div class="mt-4">
        <form method="POST" action="{{ route('admin.kyc.approve', $document) }}" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded mr-2">Approve</button>
        </form>
        <form method="POST" action="{{ route('admin.kyc.reject', $document) }}" class="inline" onsubmit="return setReason(event)">
            @csrf
            <input type="text" name="reason" placeholder="Rejection reason" class="border rounded px-3 py-2 mr-2" required>
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Reject</button>
        </form>
    </div>
</div>
@endsection
