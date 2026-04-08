@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Edit Plan: {{ $plan->name }}</h2>
    <a href="{{ route('admin.plans.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded">Back</a>
</div>

<div class="bg-white p-6 rounded-lg shadow max-w-lg">
    <form method="POST" action="{{ route('admin.plans.update', $plan) }}">
        @csrf @method('PUT')
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name', $plan->name) }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description', $plan->description) }}</textarea>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Min Amount (KES)</label>
            <input type="number" name="min_amount" value="{{ old('min_amount', $plan->min_amount) }}" step="0.01" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Max Amount (KES)</label>
            <input type="number" name="max_amount" value="{{ old('max_amount', $plan->max_amount) }}" step="0.01" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Daily Interest Rate (%)</label>
            <input type="number" name="daily_interest_rate" value="{{ old('daily_interest_rate', $plan->daily_interest_rate) }}" step="0.01" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Duration (days)</label>
            <input type="number" name="duration_days" value="{{ old('duration_days', $plan->duration_days) }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Active?</label>
            <select name="is_active" class="w-full border rounded px-3 py-2">
                <option value="1" {{ $plan->is_active ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ !$plan->is_active ? 'selected' : '' }}>No</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update Plan</button>
    </form>
</div>
@endsection
