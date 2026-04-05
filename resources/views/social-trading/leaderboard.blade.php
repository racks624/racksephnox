@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold golden-title">🏆 Top Traders Leaderboard</h1>
            <p class="text-gold-400 mt-2">Discover and follow the most profitable traders</p>
        </div>

        <div class="card-golden p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gold/30">
                        指数
                            <th class="px-4 py-3 text-left">Rank</th>
                            <th class="px-4 py-3 text-left">Trader</th>
                            <th class="px-4 py-3 text-left">Total P&L</th>
                            <th class="px-4 py-3 text-left">Win Rate</th>
                            <th class="px-4 py-3 text-left">Total Trades</th>
                            <th class="px-4 py-3 text-left">Followers</th>
                            <th class="px-4 py-3 text-left">Action</th>
                         </thead>
                    <tbody class="divide-y divide-gold/20">
                        @foreach($topTraders as $index => $trader)
                        <tr class="hover:bg-gold/5 transition">
                            <td class="px-4 py-3">
                                @if($index == 0) 🥇
                                @elseif($index == 1) 🥈
                                @elseif($index == 2) 🥉
                                @else {{ $index + 1 }}
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gold/20 flex items-center justify-center">
                                        <i class="fas fa-user-circle text-gold text-xl"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('social-trading.profile', $trader->username) }}" class="font-semibold text-gold hover:underline">
                                            {{ $trader->username }}
                                        </a>
                                        <p class="text-xs text-ivory/50">{{ $trader->user->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 {{ $trader->total_pnl >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                KES {{ number_format($trader->total_pnl, 2) }}
                            </td>
                            <td class="px-4 py-3">{{ number_format($trader->win_rate, 1) }}%</td>
                            <td class="px-4 py-3">{{ number_format($trader->total_trades) }}</td>
                            <td class="px-4 py-3">{{ number_format($trader->followers_count) }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('social-trading.profile', $trader->username) }}" class="btn-golden text-sm py-1 px-3">View Profile</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
