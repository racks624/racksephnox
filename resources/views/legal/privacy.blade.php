@extends('layouts.guest')
@section('content')
<div class="text-center">
    <h1 class="text-2xl font-bold text-gold mb-4">Privacy Policy</h1>
    <div class="text-left space-y-4 text-ivory/80 text-sm">
        <p>We collect your name, email, and phone number for account verification and security purposes.</p>
        <p>Your personal data is encrypted and never shared with third parties without your explicit consent.</p>
        <p>You may request data deletion at any time by contacting support.</p>
        <p>We use cookies to enhance your experience. By using our platform, you agree to our use of cookies.</p>
    </div>
    <div class="mt-6"><a href="{{ route('register') }}" class="text-gold-400 hover:text-gold text-sm">← Back to Registration</a></div>
</div>
@endsection
