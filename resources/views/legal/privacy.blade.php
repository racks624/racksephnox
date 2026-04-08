@extends('layouts.guest')

@section('content')
<div class="text-center">
    <h1 class="text-2xl font-bold text-yellow-500 mb-4">Privacy Policy</h1>
    <div class="text-left space-y-4 text-gray-300">
        <p>We collect your name, email, and phone number for account verification.</p>
        <p>Your data is encrypted and never shared with third parties.</p>
        <p>You can request data deletion at any time.</p>
    </div>
    <div class="mt-6">
        <a href="{{ route('register') }}" class="text-yellow-500 hover:underline">← Back to Registration</a>
    </div>
</div>
@endsection
