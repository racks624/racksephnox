@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold text-gold">🌀 Lottery Spin History</h1>
                <a href="{{ route('lottery.index') }}" class="btn-golden text-sm py-1 px-3">← Back to Game</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gold/30">
                        <tr>
                            <th class="px-4 py-2 text-left">Date</th>
                            <th class="px-4 py-2 text-left">Bet</th>
                            <th class="px-4 py-2 text-left">Win</th>
                            <th class="px-4 py-2 text-left">Result</th>
                            <th class="px-4 py-2 text-left">Jackpot</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $spin)
                        <tr>
                            <td class="px-4 py-2">{{ $spin->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-2">KES {{ number_format($spin->bet_amount, 2) }}</td>
                            <td class="px-4 py-2 text-green-400">KES {{ number_format($spin->win_amount, 2) }}</td>
                            <td class="px-4 py-2">
                                <div class="flex gap-1 text-xl">
                                    @foreach($spin->result['names'] ?? [] as $name)
                                        @if($name == 'divine_sword') ⚔️
                                        @elseif($name == 'divine_bell') 🔔
                                        @elseif($name == 'golden_flower') 🌸
                                        @elseif($name == 'frequency_8888') 8888
                                        @elseif($name == 'frequency_7777') 7777
                                        @elseif($name == 'taurus') ♉
                                        @elseif($name == 'tree_of_life') 🌳
                                        @elseif($name == 'divine_star') ⭐
                                        @else ❓
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                @if($spin->result['super_jackpot'] ?? false)
                                    <span class="text-gold-400">🌟 Super</span>
                                @elseif($spin->result['mini_jackpot'] ?? false)
                                    <span class="text-pink-400">🌸 Mini</span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-8 text-ivory/50">No spins yet. Start your cosmic journey!</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $history->links() }}
        </div>
    </div>
</div>
@endsection
