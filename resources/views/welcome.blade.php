@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary">
    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
            <div class="flex justify-center mb-8">
                <img src="{{ asset('img/logo.svg') }}" alt="Racksephnox" class="h-24 w-auto">
            </div>
            <h1 class="text-5xl font-bold text-primary mb-4">Racksephnox</h1>
            <p class="text-xl text-secondary mb-8">Cryptocurrency Bitcoin Platform</p>
            <p class="text-lg text-secondary max-w-2xl mx-auto mb-12">
                Invest in the future with our secure, transparent, and high‑yield cryptocurrency investment platform.
                Earn daily returns and grow your wealth with Racksephnox.
            </p>
            @guest
                <div class="space-x-4">
                    <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-tertiary transition">
                        <i class="fas fa-user-plus mr-2"></i> Get Started
                    </a>
                </div>
            @else
                <div>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition">
                        <i class="fas fa-chart-line mr-2"></i> Go to Dashboard
                    </a>
                </div>
            @endguest
        </div>
    </div>

    <!-- Features Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-3xl font-bold text-center text-primary mb-12">Why Choose Racksephnox?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="card p-6 text-center">
                <i class="fas fa-chart-line text-4xl text-primary mb-4"></i>
                <h3 class="text-xl font-semibold text-primary mb-2">Daily Profits</h3>
                <p class="text-secondary">Earn interest every day, credited directly to your wallet.</p>
            </div>
            <div class="card p-6 text-center">
                <i class="fas fa-shield-alt text-4xl text-primary mb-4"></i>
                <h3 class="text-xl font-semibold text-primary mb-2">Secure & Verified</h3>
                <p class="text-secondary">KYC verification and secure M-Pesa integration.</p>
            </div>
            <div class="card p-6 text-center">
                <i class="fas fa-microchip text-4xl text-primary mb-4"></i>
                <h3 class="text-xl font-semibold text-primary mb-2">Flexible Plans</h3>
                <p class="text-secondary">Choose from 6 machine series with VIP levels.</p>
            </div>
        </div>
    </div>
</div>
@endsection
