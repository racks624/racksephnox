@extends('layouts.guest')
@section('content')
<div class="text-center">
    <h1 class="text-2xl font-bold text-gold mb-4">Terms of Service</h1>
    <div class="text-left space-y-4 text-ivory/80 text-sm">
        <p>By accessing or using Racksephnox, you agree to be bound by these terms.</p>
        <p>Cryptocurrency and high‑yield investments carry significant risk. You may lose your entire capital. Invest only what you can afford to lose.</p>
        <p>Racksephnox is not a financial advisor. All investment decisions are your own.</p>
        <p>We reserve the right to suspend or terminate accounts that violate our policies.</p>
        <p>These terms may be updated from time to time. Continued use constitutes acceptance.</p>
    </div>
    <div class="mt-6"><a href="{{ route('register') }}" class="text-gold-400 hover:text-gold text-sm">← Back to Registration</a></div>
</div>
@endsection
