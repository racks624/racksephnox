@extends('layouts.app')
@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold golden-title mb-6">📊 My Investments</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="card-golden p-4 text-center"><p class="text-ivory/60">Total Invested</p><p class="text-2xl font-bold text-gold">KES {{ number_format($totalInvested, 2) }}</p></div>
            <div class="card-golden p-4 text-center"><p class="text-ivory/60">Total Profit</p><p class="text-2xl font-bold text-green-400">KES {{ number_format($totalProfit, 2) }}</p></div>
            <div class="card-golden p-4 text-center"><p class="text-ivory/60">Active Investments</p><p class="text-2xl font-bold text-gold">{{ $activeCount }}</p></div>
        </div>
        @if($machineInvestments->count())
        <div class="card-golden p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gold/30">
                        <tr><th class="px-4 py-3 text-left">Machine</th><th>VIP</th><th>Amount</th><th>Daily Profit</th><th>Profit Earned</th><th>Status</th><th>End Date</th><th></th></tr>
                    </thead>
                    <tbody class="divide-y divide-gold/20">
                        @foreach($machineInvestments as $inv)
                        <tr>
                            <td class="px-4 py-3">{{ $inv->machine->name }}</td>
                            <td class="px-4 py-3">VIP {{ $inv->vip_level }}</td>
                            <td class="px-4 py-3">KES {{ number_format($inv->amount, 2) }}</td>
                            <td class="px-4 py-3 text-green-400">+KES {{ number_format($inv->daily_profit, 2) }}</td>
                            <td class="px-4 py-3">KES {{ number_format($inv->profit_credited, 2) }}</td>
                            <td class="px-4 py-3">
                                @if($inv->status == 'active') <span class="text-green-400">● Active</span>
                                @elseif($inv->status == 'completed') <span class="text-gold">✓ Completed</span>
                                @else <span class="text-red-400">✗ Cancelled</span> @endif
                            </td>
                            <td class="px-4 py-3">{{ $inv->end_date->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                @if($inv->status == 'active')
                                <button onclick="earlyWithdraw({{ $inv->id }})" class="text-red-400 text-sm hover:text-red-300">Withdraw</button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $machineInvestments->links() }}
        </div>
        @else
        <div class="card-golden p-6 text-center"><p class="text-ivory/50">No investments yet. <a href="{{ route('machines.index') }}" class="text-gold-400">Explore Machines →</a></p></div>
        @endif
    </div>
</div>
<script>
function earlyWithdraw(investmentId) {
    if(confirm('⚠️ Early withdrawal will incur a penalty (20%). Proceed?')) {
        fetch(`/machines/${investmentId}/early-withdraw`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' } })
            .then(res => res.json()).then(data => { alert(data.message); if(data.success) location.reload(); })
            .catch(err => alert('Error: '+err.message));
    }
}
</script>
@endsection
