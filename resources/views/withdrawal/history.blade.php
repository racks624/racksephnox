@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <h1 class="text-2xl font-bold text-gold mb-4">📜 Withdrawal History</h1>
            @if($withdrawals->count())
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gold/30">
                            指数
                                <th class="px-4 py-2 text-left">Amount</th>
                                <th class="px-4 py-2 text-left">Fee</th>
                                <th class="px-4 py-2 text-left">Net</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Date</th>
                             </thead>
                        <tbody class="divide-y divide-gold/20">
                            @foreach($withdrawals as $wd)
                            <tr>
                                <td class="px-4 py-2">KES {{ number_format($wd->amount, 2) }}</td>
                                <td class="px-4 py-2">- KES {{ number_format($wd->fee, 2) }}</td>
                                <td class="px-4 py-2">KES {{ number_format($wd->net_amount, 2) }}</td>
                                <td class="px-4 py-2">
                                    @if($wd->status == 'pending')
                                        <span class="text-yellow-400">⏳ Pending</span>
                                    @elseif($wd->status == 'processing')
                                        <span class="text-blue-400">🔄 Processing</span>
                                    @elseif($wd->status == 'completed')
                                        <span class="text-green-400">✅ Completed</span>
                                    @else
                                        <span class="text-red-400">❌ Rejected</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ $wd->created_at->format('Y-m-d H:i') }}</td>
                             </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $withdrawals->links() }}
            @else
                <p class="text-center text-ivory/50 py-8">No withdrawal requests yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
