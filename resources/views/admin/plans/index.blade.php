@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Investment Plans</h2>
    <a href="{{ route('admin.plans.create') }}" class="px-4 py-2 bg-green-600 text-white rounded">+ New Plan</a>
</div>

<table class="min-w-full bg-white rounded-lg">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-6 py-3 text-left">Name</th>
            <th class="px-6 py-3 text-left">Min</th>
            <th class="px-6 py-3 text-left">Max</th>
            <th class="px-6 py-3 text-left">Daily %</th>
            <th class="px-6 py-3 text-left">Days</th>
            <th class="px-6 py-3 text-left">Active</th>
            <th class="px-6 py-3 text-left">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($plans as $plan)
        <tr class="border-t">
            <td class="px-6 py-4">{{ $plan->name }}</td>
            <td class="px-6 py-4">KES {{ number_format($plan->min_amount) }}</td>
            <td class="px-6 py-4">KES {{ number_format($plan->max_amount) }}</td>
            <td class="px-6 py-4">{{ $plan->daily_interest_rate }}%</td>
            <td class="px-6 py-4">{{ $plan->duration_days }}</td>
            <td class="px-6 py-4">{{ $plan->is_active ? 'Yes' : 'No' }}</td>
            <td class="px-6 py-4">
                <a href="{{ route('admin.plans.edit', $plan) }}" class="text-green-600 hover:underline mr-2">Edit</a>
                <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" class="inline" onsubmit="return confirm('Delete plan?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
