@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="card-golden p-8">
            <h1 class="text-3xl font-bold text-gold text-center mb-6">Welcome to Racksephnox</h1>
            <p class="text-ivory/70 text-center mb-8">Let's get you started on your investment journey.</p>

            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-gold/20 flex items-center justify-center text-gold font-bold">1</div>
                    <div>
                        <h3 class="font-semibold text-gold">Complete KYC</h3>
                        <p class="text-sm text-ivory/60">Verify your identity to unlock all features.</p>
                    </div>
                    <a href="{{ route('kyc') }}" class="ml-auto btn-golden text-sm">Go to KYC</a>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-gold/20 flex items-center justify-center text-gold font-bold">2</div>
                    <div>
                        <h3 class="font-semibold text-gold">Make a Deposit</h3>
                        <p class="text-sm text-ivory/60">Add funds to your wallet via M-Pesa.</p>
                    </div>
                    <a href="{{ route('mpesa.deposit') }}" class="ml-auto btn-golden text-sm">Deposit</a>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-gold/20 flex items-center justify-center text-gold font-bold">3</div>
                    <div>
                        <h3 class="font-semibold text-gold">Start Investing</h3>
                        <p class="text-sm text-ivory/60">Choose a plan and watch your wealth grow.</p>
                    </div>
                    <a href="{{ route('web.investments') }}" class="ml-auto btn-golden text-sm">Invest</a>
                </div>
            </div>

            <div class="mt-8 text-center">
                <form method="POST" action="{{ route('onboarding.store') }}">
                    @csrf
                    <button type="submit" class="btn-golden">Complete Setup</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
