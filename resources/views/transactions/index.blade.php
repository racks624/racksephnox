@extends('layouts.app')

@section('content')
<div x-data="transactionManager()" x-init="init()" class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Transaction History</h1>
            <div class="flex gap-2">
                <button @click="exportData()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-file-excel mr-2"></i>Export CSV
                </button>
                <button @click="printPage()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Credits</p>
                        <p class="text-2xl font-bold text-green-600">KES {{ number_format($summary['total_credits'], 2) }}</p>
                    </div>
                    <i class="fas fa-arrow-up text-green-500 text-2xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Debits</p>
                        <p class="text-2xl font-bold text-red-600">KES {{ number_format($summary['total_debits'], 2) }}</p>
                    </div>
                    <i class="fas fa-arrow-down text-red-500 text-2xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Transactions</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($summary['total_transactions']) }}</p>
                    </div>
                    <i class="fas fa-exchange-alt text-blue-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6 dark:bg-gray-800">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Type</label>
                    <select name="type" class="w-full border rounded-lg px-3 py-2">
                        <option value="">All</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">From Date</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">To Date</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Min Amount</label>
                    <input type="number" name="min_amount" value="{{ request('min_amount') }}" step="0.01" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Max Amount</label>
                    <input type="number" name="max_amount" value="{{ request('max_amount') }}" step="0.01" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Description..." class="w-full border rounded-lg px-3 py-2">
                </div>
                <div class="md:col-span-6 flex justify-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                        <i class="fas fa-undo mr-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Sort Controls -->
        <div class="flex justify-end mb-4 gap-2">
            <span class="text-sm text-gray-500">Sort by:</span>
            <a href="{{ route('transactions.index', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
               class="text-sm {{ request('sort') == 'created_at' ? 'text-blue-600 font-semibold' : 'text-gray-500' }}">
                Date <i class="fas fa-sort-{{ request('direction') == 'asc' && request('sort') == 'created_at' ? 'up' : 'down' }}"></i>
            </a>
            <a href="{{ route('transactions.index', array_merge(request()->query(), ['sort' => 'amount', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
               class="text-sm {{ request('sort') == 'amount' ? 'text-blue-600 font-semibold' : 'text-gray-500' }}">
                Amount <i class="fas fa-sort-{{ request('direction') == 'asc' && request('sort') == 'amount' ? 'up' : 'down' }}"></i>
            </a>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        指数
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Reference</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($transactions as $tx)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ $tx->created_at->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if(in_array($tx->type, ['credit', 'deposit', 'interest'])) bg-green-100 text-green-800
                                    @elseif(in_array($tx->type, ['debit', 'withdrawal'])) bg-red-100 text-red-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ ucfirst($tx->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $tx->description }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $tx->amount > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($tx->balance_after, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tx->reference ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2 block"></i>
                                No transactions found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    function transactionManager() {
        return {
            init() {},
            exportData() {
                window.location.href = '{{ route('transactions.export', request()->query()) }}';
            },
            printPage() {
                window.print();
            }
        }
    }
</script>
@endsection
