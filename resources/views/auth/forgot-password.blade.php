@extends('layouts.guest')

@section('content')
<div class="space-y-6">
    <div class="text-center">
        <div class="mx-auto w-16 h-16 bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-2xl flex items-center justify-center">
            <span class="text-2xl font-black text-white">R</span>
        </div>
        <h2 class="mt-4 text-2xl font-bold text-white">Reset Password</h2>
        <p class="mt-1 text-sm text-yellow-400">Enter your email to receive reset link</p>
    </div>

    @if(session('status'))
        <div class="bg-green-500/20 border border-green-500 rounded-lg p-3">
            <p class="text-green-400 text-sm">{{ session('status') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-500/20 border border-red-500 rounded-lg p-3">
            @foreach($errors->all() as $error)
                <p class="text-red-400 text-xs">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <label class="block text-sm font-medium text-yellow-400 mb-1">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full px-4 py-2 border border-yellow-500/30 rounded-lg bg-gray-900/50 text-white focus:outline-none focus:ring-2 focus:ring-yellow-500"
                placeholder="you@example.com">
        </div>

        <button type="submit" class="w-full mt-4 py-3 px-4 bg-gradient-to-r from-yellow-500 to-yellow-700 text-gray-900 font-bold rounded-lg hover:from-yellow-600 hover:to-yellow-800 transition">
            <i class="fas fa-paper-plane mr-2"></i> Send Reset Link
        </button>
    </form>

    <div class="text-center">
        <a href="{{ route('login') }}" class="text-sm text-yellow-500 hover:text-yellow-400">
            ← Back to Login
        </a>
    </div>
</div>
@endsection
