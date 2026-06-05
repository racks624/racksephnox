@extends('layouts.app')
@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6 text-center mb-6">
            <h1 class="text-3xl font-bold golden-title">💰 My Wallet</h1>
            <p class="text-5xl font-bold text-gold mt-4">KES {{ number_format($wallet->balance ?? 0, 2) }}</p>
            <div class="flex justify-center gap-4 mt-6">
                <a href="{{ route('deposit.form') }}" class="btn-golden">Deposit</a>
                <a href="{{ route('withdrawal.form') }}" class="btn-outline-silver">Withdraw</a>
            </div>
        </div>
        <div class="card-golden p-6">
            <h2 class="text-xl font-bold text-gold mb-4">📜 Transaction History</h2>
            @if($transactions->count())
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
            @else <p class="text-center text-ivory/50 py-8">No transactions yet.</p> @endif
        </div>
    </div>
</div>
@endsection
