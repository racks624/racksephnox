@extends('layouts.guest')

@section('content')
<div class="space-y-6">
    <div class="text-center">
        <div class="mx-auto w-16 h-16 bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-2xl flex items-center justify-center">
            <span class="text-2xl font-black text-white">R</span>
        </div>
        <h2 class="mt-4 text-2xl font-bold text-white">Create Account</h2>
        <p class="mt-1 text-sm text-yellow-400">Join Racksephnox Crypto Platform</p>
    </div>

    @if($errors->any())
        <div class="bg-red-500/20 border border-red-500 rounded-lg p-3">
            @foreach($errors->all() as $error)
                <p class="text-red-400 text-xs">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-yellow-400 mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border border-yellow-500/30 rounded-lg bg-gray-900/50 text-white focus:outline-none focus:ring-2 focus:ring-yellow-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-yellow-400 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2 border border-yellow-500/30 rounded-lg bg-gray-900/50 text-white focus:outline-none focus:ring-2 focus:ring-yellow-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-yellow-400 mb-1">Phone Number</label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-yellow-500/30 bg-gray-900/50 text-yellow-400">+254</span>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                        class="flex-1 px-4 py-2 border border-yellow-500/30 rounded-r-lg bg-gray-900/50 text-white focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="712345678">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-yellow-400 mb-1">Referral Code (Optional)</label>
                <input type="text" name="referral_code" value="{{ old('referral_code') }}"
                    class="w-full px-4 py-2 border border-yellow-500/30 rounded-lg bg-gray-900/50 text-white focus:outline-none focus:ring-2 focus:ring-yellow-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-yellow-400 mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2 border border-yellow-500/30 rounded-lg bg-gray-900/50 text-white focus:outline-none focus:ring-2 focus:ring-yellow-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-yellow-400 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-4 py-2 border border-yellow-500/30 rounded-lg bg-gray-900/50 text-white focus:outline-none focus:ring-2 focus:ring-yellow-500">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="terms" id="terms" required class="mr-2">
                <label for="terms" class="text-xs text-yellow-400/80">
                    I agree to the <a href="{{ route('terms') }}" target="_blank" class="text-yellow-500 hover:underline">Terms of Service</a>
                </label>
            </div>

            <button type="submit"
                class="w-full py-3 px-4 bg-gradient-to-r from-yellow-500 to-yellow-700 text-gray-900 font-bold rounded-lg hover:from-yellow-600 hover:to-yellow-800 transition">
                <i class="fas fa-gem mr-2"></i> Register
            </button>
        </div>
    </form>

    <div class="text-center">
        <a href="{{ route('login') }}" class="text-sm text-yellow-500 hover:text-yellow-400">
            Already have an account? Sign in →
        </a>
    </div>
</div>
@endsection
