@extends('admin.layouts.app')

@section('content')
<div class="admin-card p-5">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gold">📈 Investment Plans</h1>
        <a href="{{ route('admin.plans.create') }}" class="btn-golden text-sm py-2 px-4">+ New Plan</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-gold/30">
                <tr class="text-gold-400">
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Min (KES)</th>
                    <th class="px-4 py-3 text-left">Max (KES)</th>
                    <th class="px-4 py-3 text-left">Daily %</th>
                    <th class="px-4 py-3 text-left">Days</th>
                    <th class="px-4 py-3 text-left">Active</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </td>
            </thead>
            <tbody>
                @foreach($plans as $plan)
                <tr class="border-b border-gold/20 hover:bg-gold/5">
                    <td class="px-4 py-3 font-medium text-ivory">{{ $plan->name }}</td>
                    <td class="px-4 py-3">{{ number_format($plan->min_amount) }}</td>
                    <td class="px-4 py-3">{{ number_format($plan->max_amount) }}</td>
                    <td class="px-4 py-3 text-green-400">{{ $plan->daily_interest_rate }}%</td>
                    <td class="px-4 py-3">{{ $plan->duration_days }}</td>
                    <td class="px-4 py-3">@if($plan->is_active) <span class="text-green-400">✅ Active</span> @else <span class="text-red-400">❌ Inactive</span> @endif</td>
                    <td class="px-4 py-3 flex gap-2">
                        <a href="{{ route('admin.plans.edit', $plan) }}" class="text-gold-400 hover:text-gold">Edit</a>
                        <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" onsubmit="return confirm('Delete this plan?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $plans->links() }}
</div>
@endsection
