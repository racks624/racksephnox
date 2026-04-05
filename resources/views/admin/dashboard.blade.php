@extends('admin.layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gold mb-6">Admin Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card-golden p-4 text-center">
            <p class="text-sm text-ivory/60">Total Users</p>
            <p class="text-3xl font-bold text-gold">{{ number_format($totalUsers) }}</p>
        </div>
        <div class="card-golden p-4 text-center">
            <p class="text-sm text-ivory/60">Verified Users</p>
            <p class="text-3xl font-bold text-gold">{{ number_format($verifiedUsers) }}</p>
        </div>
        <div class="card-golden p-4 text-center">
            <p class="text-sm text-ivory/60">Total Invested</p>
            <p class="text-3xl font-bold text-gold">KES {{ number_format($totalInvested, 2) }}</p>
        </div>
        <div class="card-golden p-4 text-center">
            <p class="text-sm text-ivory/60">Pending Actions</p>
            <p class="text-3xl font-bold text-gold">Dep: {{ $pendingDeposits }} | Wd: {{ $pendingWithdrawals }}</p>
        </div>
    </div>

    <div class="card-golden p-6">
        <h2 class="text-xl font-bold text-gold mb-4">Recent Users</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-gold/30">
                    指数
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Joined</th>
                     </thead>
                <tbody>
                    @foreach($recentUsers as $user)
                    <tr class="border-b border-gold/20">
                        <td class="px-4 py-2">{{ $user->name }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2">{{ $user->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
