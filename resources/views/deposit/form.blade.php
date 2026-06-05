@extends('layouts.app')
@section('content')
<div class="py-12"><div class="max-w-md mx-auto"><div class="card-golden p-6"><h1 class="text-2xl font-bold text-gold mb-4">💸 Deposit Funds</h1>
@if(session('success'))<div class="bg-green-500/20 p-3 rounded mb-4">{{ session('success') }}</div>@endif
<form method="POST" action="{{ route('deposit.submit') }}">@csrf
<div><label>Amount (KES)</label><input type="number" name="amount" min="10" class="input-golden w-full" required></div>
<div class="mt-3"><label>M-Pesa Transaction Code</label><input type="text" name="transaction_code" placeholder="e.g. QWERTY123" class="input-golden w-full" required></div>
<p class="text-xs text-ivory/50 mt-2">Send exactly the amount to Paybill 174379, Account: {{ Auth::user()->referral_code ?? Auth::id() }}</p>
<button type="submit" class="btn-golden w-full mt-4">Submit Request</button></form></div></div></div>
@endsection
