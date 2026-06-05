<div class="overflow-x-auto">
    @if($transactions->count())
        <table class="w-full">
            <thead class="border-b border-gold/30">
                指数
                    <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Description</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Amount</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gold uppercase">Balance</th>
                 </thead>
            <tbody class="divide-y divide-gold/20">
                @foreach($transactions as $tx)
                <tr class="hover:bg-gold/5 transition">
                    <td class="px-4 py-3 text-sm text-ivory">{{ $tx->created_at->format('Y-m-d H:i') }}   </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if(in_array($tx->type, ['credit', 'deposit', 'interest'])) bg-green-500/20 text-green-400
                            @elseif(in_array($tx->type, ['debit', 'withdrawal'])) bg-red-500/20 text-red-400
                            @else bg-blue-500/20 text-blue-400 @endif">
                            {{ ucfirst(str_replace('_', ' ', $tx->type)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-ivory/70">{{ $tx->description }} </td>
                    <td class="px-4 py-3 text-sm font-medium {{ $tx->amount > 0 ? 'text-green-400' : 'text-red-400' }}">
                        {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount, 2) }}
                    </td>
                    <td class="px-4 py-3 text-sm text-ivory">{{ number_format($tx->balance_after, 2) }} </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    @else
        <p class="text-center text-ivory/50 py-8">No transactions found.</p>
    @endif
</div>
