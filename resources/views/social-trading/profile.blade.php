@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Profile Header -->
        <div class="card-golden p-6 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded-full bg-gold/20 flex items-center justify-center text-4xl text-gold">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gold">{{ $profile->username }}</h1>
                        <p class="text-ivory/70">{{ $profile->user->name }}</p>
                        <p class="text-sm text-ivory/50 mt-1">Member since {{ $profile->created_at->format('M Y') }}</p>
                        @if($profile->bio)
                            <p class="text-ivory/70 mt-2 max-w-md">{{ $profile->bio }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex gap-3">
                    @auth
                        @if(Auth::id() == $profile->user_id)
                            <button onclick="showEditModal()" class="btn-golden">Edit Profile</button>
                        @else
                            @if($isFollowing)
                                <form method="POST" action="{{ route('social-trading.unfollow', $profile->user) }}">
                                    @csrf
                                    <button type="submit" class="btn-golden bg-red-600 hover:bg-red-700">Unfollow</button>
                                </form>
                            @else
                                <button onclick="showFollowModal()" class="btn-golden">Follow</button>
                            @endif
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-gold/20">
                <div class="text-center">
                    <p class="text-ivory/60 text-sm">Total P&L</p>
                    <p class="text-2xl font-bold {{ $profile->total_pnl >= 0 ? 'text-green-400' : 'text-red-400' }}">
                        KES {{ number_format($profile->total_pnl, 2) }}
                    </p>
                </div>
                <div class="text-center">
                    <p class="text-ivory/60 text-sm">Win Rate</p>
                    <p class="text-2xl font-bold text-gold">{{ number_format($profile->win_rate, 1) }}%</p>
                </div>
                <div class="text-center">
                    <p class="text-ivory/60 text-sm">Total Trades</p>
                    <p class="text-2xl font-bold text-gold">{{ number_format($profile->total_trades) }}</p>
                </div>
                <div class="text-center">
                    <p class="text-ivory/60 text-sm">Followers</p>
                    <p class="text-2xl font-bold text-gold">{{ number_format($followersCount) }}</p>
                </div>
            </div>
        </div>

        <!-- Recent Trades -->
        <div class="card-golden p-6">
            <h2 class="text-xl font-bold text-gold mb-4">Recent Trades</h2>
            @if($recentTrades->count())
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-gold/30">
                            指数
                                <th class="px-4 py-2 text-left">Date</th>
                                <th class="px-4 py-2 text-left">Type</th>
                                <th class="px-4 py-2 text-left">Amount (BTC)</th>
                                <th class="px-4 py-2 text-left">Price</th>
                                <th class="px-4 py-2 text-left">Total</th>
                             </thead>
                        <tbody class="divide-y divide-gold/20">
                            @foreach($recentTrades as $trade)
                            <tr class="hover:bg-gold/5 transition">
                                <td class="px-4 py-2">{{ $trade->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $trade->side === 'buy' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                        {{ strtoupper($trade->side) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2">{{ number_format($trade->filled_amount, 6) }}</td>
                                <td class="px-4 py-2">KES {{ number_format($trade->price_per_btc, 2) }}</td>
                                <td class="px-4 py-2">KES {{ number_format($trade->filled_kes, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-ivory/50 py-8">No trades yet.</p>
            @endif
        </div>
    </div>
</div>

<!-- Follow Modal -->
<div id="followModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="bg-cosmic-deep border border-gold/30 rounded-2xl p-6 max-w-md w-full">
        <h3 class="text-xl font-bold text-gold mb-4">Follow {{ $profile->username }}</h3>
        <form method="POST" action="{{ route('social-trading.follow', $profile->user) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm text-gold-400 mb-1">Copy Ratio (%)</label>
                <input type="number" name="copy_ratio" value="100" step="1" min="1" max="100" class="input-golden w-full" required>
                <p class="text-xs text-ivory/50 mt-1">Percentage of each trade to copy</p>
            </div>
            <div class="mb-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="auto_copy" value="1" checked class="form-checkbox h-5 w-5 text-gold">
                    <span class="text-sm text-ivory/70">Auto-copy trades</span>
                </label>
            </div>
            <div class="mb-4">
                <label class="block text-sm text-gold-400 mb-1">Max Copy Amount (KES)</label>
                <input type="number" name="max_copy_amount" step="10" class="input-golden w-full" placeholder="Optional limit per trade">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal('followModal')" class="px-4 py-2 bg-gray-600 text-white rounded-lg">Cancel</button>
                <button type="submit" class="btn-golden">Follow</button>
            </div>
        </form>
    </div>
</div>

<script>
function showFollowModal() {
    document.getElementById('followModal').classList.remove('hidden');
    document.getElementById('followModal').classList.add('flex');
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
}
</script>
@endsection
