@extends('admin.layouts.app')
@section('content')
<div class="admin-card p-6"><h1 class="text-2xl font-bold text-gold mb-4">Create New Plan</h1>
<form method="POST" action="{{ route('admin.plans.store') }}">
    @csrf
    <div class="grid grid-cols-2 gap-4"><div><label>Name</label><input type="text" name="name" class="input-golden w-full" required></div>
    <div><label>Description (optional)</label><textarea name="description" class="input-golden w-full"></textarea></div>
    <div><label>Min Amount (KES)</label><input type="number" step="0.01" name="min_amount" class="input-golden w-full" required></div>
    <div><label>Max Amount (KES)</label><input type="number" step="0.01" name="max_amount" class="input-golden w-full" required></div>
    <div><label>Daily Interest Rate (%)</label><input type="number" step="0.01" name="daily_interest_rate" class="input-golden w-full" required></div>
    <div><label>Duration (days)</label><input type="number" name="duration_days" class="input-golden w-full" required></div>
    <div><label><input type="checkbox" name="is_active" value="1" checked> Active</label></div>
    <div><label><input type="checkbox" name="allow_auto_reinvest" value="1"> Auto-reinvest</label></div>
    <div><label><input type="checkbox" name="allow_early_withdrawal" value="1" checked> Allow early withdrawal</label></div>
    <div><label>Early withdrawal penalty (%)</label><input type="number" step="0.01" name="early_withdrawal_penalty" value="20" class="input-golden w-full"></div>
    <div><label>Max reinvestment cycles</label><input type="number" name="max_reinvestment_cycles" value="1" class="input-golden w-full"></div>
    <div><label><input type="checkbox" name="is_infinite" value="1"> Infinite plan</label></div></div>
    <button type="submit" class="btn-golden mt-4">Create Plan</button>
</form></div>
@endsection
