@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-3xl font-bold">Welcome to Racksephnox</h1>
                <p class="mt-2">Your gateway to crypto investment in Kenya.</p>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-8">
                    <div class="bg-gray-100 p-4 rounded">
                        <div class="text-sm font-medium">Total Users</div>
                        <div class="text-2xl font-bold">{{ number_format($stats['total_users']) }}</div>
                    </div>
                    <div class="bg-gray-100 p-4 rounded">
                        <div class="text-sm font-medium">Total Invested</div>
                        <div class="text-2xl font-bold">KES {{ number_format($stats['total_invested'], 2) }}</div>
                    </div>
                    <div class="bg-gray-100 p-4 rounded">
                        <div class="text-sm font-medium">Active Investments</div>
                        <div class="text-2xl font-bold">{{ number_format($stats['active_investments']) }}</div>
                    </div>
                    <div class="bg-gray-100 p-4 rounded">
                        <div class="text-sm font-medium">Total Profit Paid</div>
                        <div class="text-2xl font-bold">KES {{ number_format($stats['total_profit_paid'], 2) }}</div>
                    </div>
                </div>

                @if($cryptoPrices->count())
                <div class="mt-8">
                    <h2 class="text-xl font-semibold mb-4">Live Crypto Prices</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($cryptoPrices as $price)
                        <div class="bg-gray-100 p-4 rounded">
                            <div class="font-bold">{{ $price->symbol }}</div>
                            <div>KES {{ number_format($price->price_kes, 2) }}</div>
                            <div class="text-xs text-gray-500">Updated {{ $price->last_updated->diffForHumans() }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
