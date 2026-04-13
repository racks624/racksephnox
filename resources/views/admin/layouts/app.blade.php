<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - Racksephnox</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .admin-sidebar { background: linear-gradient(180deg, #0F172A 0%, #1E1B2E 100%); border-right: 1px solid rgba(212,175,55,0.2); }
        .admin-sidebar a.active { background: rgba(212,175,55,0.15); border-left: 3px solid #D4AF37; color: #D4AF37; }
        .admin-card { background: rgba(15,25,35,0.6); backdrop-filter: blur(12px); border: 1px solid rgba(212,175,55,0.3); border-radius: 1rem; transition: all 0.2s; }
        .admin-card:hover { border-color: rgba(212,175,55,0.6); transform: translateY(-2px); }
        .scrollbar-thin::-webkit-scrollbar { width: 4px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: #1e293b; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: #D4AF37; border-radius: 4px; }
        .btn-golden { background: linear-gradient(135deg, #D4AF37, #B8860B); color: #0F172A; font-weight: 600; padding: 0.5rem 1rem; border-radius: 0.75rem; transition: all 0.2s; }
        .btn-golden:hover { transform: scale(1.02); filter: brightness(1.1); }
        .input-golden { background: rgba(15,25,35,0.8); border: 1px solid rgba(212,175,55,0.4); border-radius: 0.75rem; padding: 0.5rem 1rem; color: #F5F5F5; }
        .input-golden:focus { outline: none; border-color: #D4AF37; }
    </style>
</head>
<body class="bg-cosmic-void text-ivory font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <aside class="admin-sidebar w-64 flex-shrink-0 overflow-y-auto scrollbar-thin">
            <div class="p-5 text-center border-b border-gold/20">
                <div class="w-12 h-12 mx-auto bg-gradient-to-r from-gold-400 to-gold-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-crown text-white text-xl"></i>
                </div>
                <h2 class="mt-3 text-lg font-bold golden-title">Racksephnox Admin</h2>
                <p class="text-xs text-gold-400/70">Divine Governance</p>
            </div>
            <nav class="p-3 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all hover:bg-gold/10 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fas fa-tachometer-alt w-5"></i> Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all hover:bg-gold/10 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="fas fa-users w-5"></i> Users</a>
                <a href="{{ route('admin.plans.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all hover:bg-gold/10 {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}"><i class="fas fa-chart-line w-5"></i> Investment Plans</a>
                <a href="{{ route('admin.kyc.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all hover:bg-gold/10 {{ request()->routeIs('admin.kyc.*') ? 'active' : '' }}"><i class="fas fa-id-card w-5"></i> KYC Verification</a>
                <a href="{{ route('admin.deposits.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all hover:bg-gold/10 {{ request()->routeIs('admin.deposits.*') ? 'active' : '' }}"><i class="fas fa-arrow-down w-5"></i> Deposits</a>
                <a href="{{ route('admin.withdrawals.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all hover:bg-gold/10 {{ request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}"><i class="fas fa-arrow-up w-5"></i> Withdrawals</a>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all hover:bg-gold/10 {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"><i class="fas fa-chart-pie w-5"></i> Reports</a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all hover:bg-gold/10 {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"><i class="fas fa-sliders-h w-5"></i> Settings</a>
                <a href="{{ route('admin.lottery.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all hover:bg-gold/10 {{ request()->routeIs('admin.lottery.*') ? 'active' : '' }}"><i class="fas fa-dice-d6 w-5"></i> Lottery</a>
                <hr class="my-4 border-gold/20">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all hover:bg-gold/10"><i class="fas fa-arrow-left w-5"></i> Back to Site</a>
            </nav>
        </aside>
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-cosmic-deep/80 backdrop-blur border-b border-gold/20 px-6 py-3 flex justify-between items-center">...</header>
            <main class="flex-1 overflow-y-auto p-6 scrollbar-thin">@yield('content')</main>
        </div>
    </div>
    <script>window.axios = require('axios'); window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;</script>
    @stack('scripts')
</body>
</html>
