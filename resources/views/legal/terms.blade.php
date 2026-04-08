@extends('layouts.guest')

@section('content')
<div class="text-center">
    <h1 class="text-2xl font-bold text-yellow-500 mb-4">Terms of Service</h1>
    <div class="text-left space-y-4 text-gray-300">
        <p>By using Racksephnox, you agree to these terms.</p>
        <p>Cryptocurrency investments carry high risk. You may lose your entire investment.</p>
        <p>We are not financial advisors. Invest at your own risk.</p>
    </div>
    <div class="mt-6">
        <a href="{{ route('register') }}" class="text-yellow-500 hover:underline">← Back to Registration</a>
    </div>
</div>
@endsection
