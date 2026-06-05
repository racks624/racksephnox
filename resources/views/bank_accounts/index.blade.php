@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gold">🏦 Bank Accounts</h1>
                <a href="{{ route('bank-accounts.create') }}" class="btn-golden">+ Add Account</a>
            </div>

            @if($accounts->count())
                <div class="space-y-4">
                    @foreach($accounts as $account)
                    <div class="bg-gold/5 rounded-lg p-4 border border-gold/20">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold text-gold">{{ $account->bank_name }}</p>
                                <p class="text-sm text-ivory/70">Account: {{ $account->account_name }}</p>
                                <p class="text-sm text-ivory/70">Number: {{ $account->account_number }}</p>
                                @if($account->branch)<p class="text-sm text-ivory/70">Branch: {{ $account->branch }}</p>@endif
                                @if($account->is_default)
                                    <span class="text-xs text-green-400 mt-1 inline-block">✓ Default Account</span>
                                @endif
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('bank-accounts.edit', $account) }}" class="text-gold-400 hover:text-gold">Edit</a>
                                <form method="POST" action="{{ route('bank-accounts.destroy', $account) }}" onsubmit="return confirm('Delete this account?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-ivory/50 py-8">No bank accounts added yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
