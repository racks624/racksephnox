@extends('layouts.app')
@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold text-gold">📋 All Transactions</h1>
                <a href="{{ route('transactions.export') }}" class="btn-golden text-sm">Export CSV</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gold/30">
                        <tr><th>Date</th><th>Type</th><th>Description</th><th>Amount</th><th>Balance</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gold/20">
                        @foreach($transactions as $tx)
                        <tr>
                            <td class="px-4 py-3">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3">{{ ucfirst($tx->type) }}</td>
                            <td class="px-4 py-3">{{ $tx->description ?? '-' }}</td>
                            <td class="px-4 py-3 {{ $tx->amount > 0 ? 'text-green-400' : 'text-red-400' }}">{{ number_format($tx->amount, 2) }}</td>
                            <td class="px-4 py-3">{{ number_format($tx->balance_after, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
