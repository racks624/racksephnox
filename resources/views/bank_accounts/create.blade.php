@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <h1 class="text-2xl font-bold text-gold mb-6">➕ Add Bank Account</h1>
            
            <form method="POST" action="{{ route('bank-accounts.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm text-gold-400 mb-1">Bank Name</label>
                    <input type="text" name="bank_name" class="input-golden w-full" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gold-400 mb-1">Account Name</label>
                    <input type="text" name="account_name" class="input-golden w-full" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gold-400 mb-1">Account Number</label>
                    <input type="text" name="account_number" class="input-golden w-full" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gold-400 mb-1">Branch (Optional)</label>
                    <input type="text" name="branch" class="input-golden w-full">
                </div>
                <div class="mb-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_default" value="1" class="form-checkbox h-5 w-5 text-gold">
                        <span class="text-sm text-ivory/70">Set as default account</span>
                    </label>
                </div>
                <button type="submit" class="btn-golden w-full">Add Account</button>
            </form>
        </div>
    </div>
</div>
@endsection
