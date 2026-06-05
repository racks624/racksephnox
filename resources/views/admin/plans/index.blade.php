@extends('admin.layouts.app')
@section('content')
<div class="admin-card p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-gold">📈 Investment Plans</h1>
        <div>
            <a href="{{ route('admin.plans.create') }}" class="btn-golden">+ Add Plan</a>
            <a href="{{ route('admin.plans.export') }}" class="btn-outline-silver ml-2">Export CSV</a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead><tr><th>Name</th><th>Min</th><th>Max</th><th>Daily Rate</th><th>Days</th><th>Active</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($plans as $plan)
                <tr><td>{{ $plan->name }}</td><td>KES {{ number_format($plan->min_amount,2) }}</td><td>KES {{ number_format($plan->max_amount,2) }}</td><td>{{ $plan->daily_interest_rate }}%</td><td>{{ $plan->duration_days }}</td><td>{{ $plan->is_active ? '✅' : '❌' }}</td>
                <td><a href="{{ route('admin.plans.edit', $plan) }}" class="text-gold-400">Edit</a> | <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="inline" onsubmit="return confirm('Delete plan?')">@csrf @method('DELETE')<button type="submit" class="text-red-400">Delete</button></form></td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $plans->links() }}
</div>
@endsection
