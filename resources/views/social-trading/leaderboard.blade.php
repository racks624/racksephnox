@extends('layouts.app')
@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold golden-title mb-6">🏆 Elite Traders</h1>
        <div class="card-golden p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gold/30">
                        <tr><th>Rank</th><th>Trader</th><th>Total P&L</th><th>Win Rate</th><th>Trades</th><th>Followers</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($topTraders as $index => $profile)
                        <tr class="border-b border-gold/20">
                            <td class="py-3">{{ $index+1 }}</td>
                            <td><a href="{{ route('social-trading.profile', $profile->username) }}" class="text-gold-400 hover:text-gold">{{ $profile->username }}</a></td>
                            <td class="text-green-400">KES {{ number_format($profile->total_pnl, 2) }}</td>
                            <td>{{ number_format($profile->win_rate, 1) }}%</td>
                            <td>{{ $profile->total_trades }}</td>
                            <td>{{ $profile->followers_count }}</td>
                            <td><button onclick="follow({{ $profile->user_id }})" class="btn-golden text-sm">Copy</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
function follow(userId) {
    if(confirm('Copy this trader?')) {
        fetch(`/social-trading/follow/${userId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({ copy_ratio: 100, auto_copy: true })
        }).then(() => location.reload());
    }
}
</script>
@endsection
