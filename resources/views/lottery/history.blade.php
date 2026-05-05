@extends('layouts.app')
@section('content')
<div class="py-12"><div class="max-w-4xl mx-auto"><div class="card-golden p-6"><h1 class="text-2xl font-bold text-gold mb-4">📜 Spin History</h1><div class="overflow-x-auto"><table class="w-full"><thead><tr><th>Date</th><th>Bet</th><th>Win</th><th>Result</th></tr></thead><tbody>@foreach($history as $spin)<tr><td>{{ $spin->created_at->format('Y-m-d H:i') }}</td><td>KES {{ number_format($spin->bet_amount,2) }}</td><td class="text-green-400">KES {{ number_format($spin->win_amount,2) }}</td><td>{{ implode(', ', $spin->result['names'] ?? []) }}</td></tr>@endforeach</tbody></table></div>{{ $history->links() }}</div></div></div>
@endsection
