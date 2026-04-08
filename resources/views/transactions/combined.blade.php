@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-6 text-gray-900">Transactions</h2>

                <!-- Account Balance Card -->
                <div class="mb-8 p-4 bg-blue-50 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900">Account Balance</h3>
                    <p class="text-3xl font-bold text-blue-600">KES {{ number_format(auth()->user()->wallet->balance, 2) }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Deposit Form -->
                    <div class="border rounded-lg p-4">
                        <h3 class="text-xl font-semibold mb-4">Deposit via M-Pesa</h3>
                        <form method="POST" action="{{ route('mpesa.deposit.initiate') }}">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Phone Number</label>
                                <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required
                                    class="w-full px-3 py-2 border rounded-md">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Amount (KES)</label>
                                <input type="number" name="amount" min="10" max="150000" step="0.01" required
                                    class="w-full px-3 py-2 border rounded-md">
                            </div>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Deposit</button>
                        </form>
                        <p class="text-xs text-gray-500 mt-2">You will receive an STK push on your phone.</p>
                    </div>

                    <!-- Withdrawal Form -->
                    <div class="border rounded-lg p-4">
                        <h3 class="text-xl font-semibold mb-4">Withdraw to M-Pesa</h3>
                        <form method="POST" action="{{ route('mpesa.withdraw.initiate') }}">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Phone Number</label>
                                <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required
                                    class="w-full px-3 py-2 border rounded-md">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Amount (KES)</label>
                                <input type="number" name="amount" min="10" step="0.01" required
                                    class="w-full px-3 py-2 border rounded-md">
                            </div>
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Withdraw</button>
                        </form>
                        <p class="text-xs text-gray-500 mt-2">Available balance: KES {{ number_format(auth()->user()->wallet->balance, 2) }}</p>
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="mt-8">
                    <h3 class="text-xl font-semibold mb-4">Recent Transactions</h3>
                    @php
                        $transactions = auth()->user()->transactions()->latest()->paginate(10);
                    @endphp
                    @if($transactions->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    指数
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Balance</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($transactions as $tx)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if(in_array($tx->type, ['credit', 'deposit', 'interest'])) bg-green-100 text-green-800
                                                @elseif(in_array($tx->type, ['debit', 'withdrawal'])) bg-red-100 text-red-800
                                                @else bg-blue-100 text-blue-800
                                                @endif">
                                                {{ ucfirst($tx->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">{{ $tx->description }}</td>
                                        <td class="px-6 py-4 {{ $tx->amount > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4">{{ number_format($tx->balance_after, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @else
                        <p class="text-gray-500">No transactions yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
