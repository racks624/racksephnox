@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <h1 class="text-2xl font-bold text-gold mb-4">📜 Deposit Requests</h1>
            @if($deposits->count())
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gold/30">
                            指数
                                <th class="px-4 py-2 text-left">Amount</th>
                                <th class="px-4 py-2 text-left">Transaction Ref</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Date</th>
                             </thead>
                        <tbody class="divide-y divide-gold/20">
                            @foreach($deposits as $deposit)
                            <tr>
                                <td class="px-4 py-2">KES {{ number_format($deposit->amount, 2) }}</td>
                                <td class="px-4 py-2">{{ $deposit->transaction_reference }}</td>
                                <td class="px-4 py-2">
                                    @if($deposit->status == 'pending')
                                        <span class="text-yellow-400">⏳ Pending</span>
                                    @elseif($deposit->status == 'verified')
                                        <span class="text-green-400">✅ Verified</span>
                                    @else
                                        <span class="text-red-400">❌ Rejected</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ $deposit->created_at->format('Y-m-d H:i') }}</td>
                             </tr>
                            @endforeach
                        </tbody>
                     </table>
                </div>
                {{ $deposits->links() }}
            @else
                <p class="text-center text-ivory/50 py-8">No deposit requests yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
