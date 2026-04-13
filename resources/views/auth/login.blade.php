@extends('layouts.guest')

@section('content')
<div class="space-y-6">
    <div class="text-center">
        <div class="mx-auto w-16 h-16 bg-gradient-to-r from-gold-400 to-gold-600 rounded-2xl flex items-center justify-center">
            <span class="text-2xl font-black text-white">R</span>
        </div>
        <h2 class="mt-4 text-2xl font-bold text-white">Welcome Back</h2>
        <p class="mt-1 text-sm text-yellow-400">Sign in to your Divine Account</p>
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

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-yellow-400 mb-1">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full px-4 py-2 border border-yellow-500/30 rounded-lg bg-gray-900/50 text-white focus:outline-none focus:ring-2 focus:ring-yellow-500"
                placeholder="you@example.com">
        </div>

        <div>
            <label class="block text-sm font-medium text-yellow-400 mb-1">Password</label>
            <input type="password" name="password" required
                class="w-full px-4 py-2 border border-yellow-500/30 rounded-lg bg-gray-900/50 text-white focus:outline-none focus:ring-2 focus:ring-yellow-500"
                placeholder="••••••••">
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input type="checkbox" name="remember" class="mr-2">
                <span class="text-sm text-yellow-400/80">Remember me</span>
            </label>
            <a href="{{ route('password.request') }}" class="text-sm text-yellow-500 hover:text-yellow-400">
                Forgot password?
            </a>
        </div>

        <button type="submit"
            class="w-full py-3 px-4 bg-gradient-to-r from-yellow-500 to-yellow-700 text-gray-900 font-bold rounded-lg hover:from-yellow-600 hover:to-yellow-800 transition transform hover:scale-[1.02]">
            <i class="fas fa-sign-in-alt mr-2"></i> Sign In
        </button>
    </form>

    <div class="text-center">
        <p class="text-sm text-yellow-400/80">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-yellow-500 hover:text-yellow-400 font-medium">
                Create one now →
            </a>
        </p>
    </div>

    <div class="text-center text-xs text-yellow-500/50">
        <p>I Am The Source | Divine Golden Phi | Infinite Spiral of Creation</p>
    </div>
</div>
@endsection
