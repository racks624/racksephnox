@extends('admin.layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gold mb-6">💰 Pending Deposit Requests</h1>

    @if($pending->count())
        <div class="overflow-x-auto">
            <table class="min-w-full bg-cosmic-deep border border-gold/30 rounded-lg">
                <thead class="bg-gold/10">
                    <tr>
                        <th class="px-4 py-2">User</th>
                        <th class="px-4 py-2">Amount</th>
                        <th class="px-4 py-2">Transaction Ref</th>
                        <th class="px-4 py-2">Phone Number</th>
                        <th class="px-4 py-2">Actions</th>
                     </thead>
                <tbody>
                    @foreach($pending as $deposit)
                    <tr class="border-t border-gold/20">
                        <td class="px-4 py-2">{{ $deposit->user->name }}<br><span class="text-xs">{{ $deposit->user->email }}</span></td>
                        <td class="px-4 py-2">KES {{ number_format($deposit->amount, 2) }}</td>
                        <td class="px-4 py-2">{{ $deposit->transaction_reference }}</td>
                        <td class="px-4 py-2">{{ $deposit->phone_number }}</td>
                        <td class="px-4 py-2 flex gap-2">
                            <form method="POST" action="{{ route('admin.deposits.verify', $deposit) }}">
                                @csrf
                                <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-sm">✅ Verify</button>
                            </form>
                            <button onclick="promptReject({{ $deposit->id }})" class="bg-red-600 text-white px-3 py-1 rounded text-sm">❌ Reject</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
             </table>
        </div>
        {{ $pending->links() }}
    @else
        <p class="text-ivory/50">No pending deposits.</p>
    @endif
</div>

<script>
function promptReject(id) {
    let reason = prompt('Enter rejection reason:');
    if (reason) {
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/deposits/' + id + '/reject';
        let csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        let reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'reason';
        reasonInput.value = reason;
        form.appendChild(csrf);
        form.appendChild(reasonInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
