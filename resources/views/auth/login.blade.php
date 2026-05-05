@extends('layouts.guest')
@section('content')
<div class="space-y-6 text-center"><h2 class="text-2xl font-bold text-white">Welcome Back</h2>
<form method="POST" action="{{ route('login') }}">@csrf
<input type="email" name="email" placeholder="Email" class="w-full px-4 py-2 border border-gold/30 rounded-lg bg-gray-900/50 text-white">
<input type="password" name="password" placeholder="Password" class="w-full px-4 py-2 border border-gold/30 rounded-lg bg-gray-900/50 text-white">
<button type="submit" class="w-full btn-golden">Sign In</button>
</form></div>
@endsection
