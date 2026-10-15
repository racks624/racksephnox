@extends('layouts.app')

@section('title', 'Deposit Status')

@section('content')
<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">

        <a href="{{ route('deposit.form') }}" class="text-gold-400 text-sm">
            <i class="fas fa-arrow-left"></i> Back to Banking Center
        </a>

        <div class="card-golden p-6 mt-4">
            <div class="text-center mb-6">
                @if($deposit->verification_status === 'verified')
                    <div class="w-16 h-16 rounded-full bg-green-500/20 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-check-circle text-green-400 text-3xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-green-400">Deposit Confirmed</h1>
                    <p class="text-ivory/70 text-sm">Your wallet has been credited.</p>
                @elseif($deposit->verification_status === 'rejected')
                    <div class="w-16 h-16 rounded-full bg-red-500/20 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-times-circle text-red-400 text-3xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-red-400">Deposit Rejected</h1>
                    <p class="text-ivory/70 text-sm">Please contact support.</p>
                @else
                    <div class="w-16 h-16 rounded-full bg-yellow-500/20 flex items-center justify-center mx-auto mb-3 animate-pulse">
                        <i class="fas fa-clock text-yellow-400 text-3xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-yellow-400">Awaiting Verification</h1>
                    <p class="text-ivory/70 text-sm">Admin will verify within 24 hours.</p>
                @endif
            </div>

            <div class="space-y-3">
                <div class="flex justify-between border-b border-gold/10 pb-2">
                    <span class="text-gold-400 text-sm">Reference</span>
                    <span class="text-ivory font-mono text-sm">{{ $deposit->reference }}</span>
                </div>
                <div class="flex justify-between border-b border-gold/10 pb-2">
                    <span class="text-gold-400 text-sm">Amount</span>
                    <span class="text-gold font-bold">KES {{ number_format($deposit->amount, 2) }}</span>
                </div>
                <div class="flex justify-between border-b border-gold/10 pb-2">
                    <span class="text-gold-400 text-sm">Transaction Code</span>
                    <span class="text-ivory font-mono">{{ $deposit->transaction_code }}</span>
                </div>
                <div class="flex justify-between border-b border-gold/10 pb-2">
                    <span class="text-gold-400 text-sm">Phone</span>
                    <span class="text-ivory font-mono text-sm">{{ $deposit->phone }}</span>
                </div>
                <div class="flex justify-between border-b border-gold/10 pb-2">
                    <span class="text-gold-400 text-sm">Status</span>
                    <span class="uppercase text-sm {{ $deposit->status_color }}">{{ $deposit->verification_status }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gold-400 text-sm">Submitted</span>
                    <span class="text-ivory/70 text-sm">{{ $deposit->created_at->format('M d, Y H:i') }}</span>
                </div>
            </div>

            @if($deposit->verification_status === 'rejected' && $deposit->rejection_reason)
                <div class="mt-4 bg-red-500/10 border border-red-500/30 rounded-lg p-3">
                    <p class="text-xs text-red-400 uppercase mb-1">Reason</p>
                    <p class="text-ivory/80 text-sm">{{ $deposit->rejection_reason }}</p>
                </div>
            @endif

            @if($deposit->match_confidence > 0)
                <div class="mt-4 text-xs text-gold-400/60 text-center">
                    Match confidence: {{ $deposit->confidence_label }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
