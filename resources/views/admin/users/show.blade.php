@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">User Details: {{ $user->name }}</h2>
    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded">Back</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Profile</h3>
        <p><strong>ID:</strong> {{ $user->id }}</p>
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Phone:</strong> {{ $user->phone }}</p>
        <p><strong>Verified:</strong> {{ $user->is_verified ? 'Yes' : 'No' }}</p>
        <p><strong>Admin:</strong> {{ $user->is_admin ? 'Yes' : 'No' }}</p>
        <p><strong>KYC Level:</strong> {{ $user->kyc_level }}</p>
        <p><strong>Joined:</strong> {{ $user->created_at->format('Y-m-d H:i') }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Wallet</h3>
        <p><strong>Balance:</strong> KES {{ number_format($user->wallet->balance, 2) }}</p>
        <p><strong>Locked:</strong> KES {{ number_format($user->wallet->locked_balance, 2) }}</p>
    </div>
</div>

<div class="mt-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
    <h3 class="text-lg font-semibold mb-4">KYC Documents</h3>
    <table class="min-w-full">
        <thead>
            <tr>
                <th class="text-left">Type</th>
                <th class="text-left">Status</th>
                <th class="text-left">Uploaded</th>
            </tr>
        </thead>
        <tbody>
            @foreach($user->kycDocuments as $doc)
            <tr>
                <td>{{ $doc->document_type }}</td>
                <td>{{ ucfirst($doc->status) }}</td>
                <td>{{ $doc->created_at->format('Y-m-d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
    <h3 class="text-lg font-semibold mb-4">Recent Transactions</h3>
    <table class="min-w-full">
        <thead>
            <tr>
                <th class="text-left">Date</th>
                <th class="text-left">Type</th>
                <th class="text-left">Amount</th>
                <th class="text-left">Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($user->transactions()->latest()->take(10)->get() as $tx)
            <tr>
                <td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $tx->type }}</td>
                <td class="{{ $tx->amount > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount, 2) }}</td>
                <td>{{ $tx->description }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
