<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Racksephnox</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cosmic-void text-ivory">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 min-h-screen bg-cosmic-deep border-r border-gold/30 p-4">
            <div class="text-center mb-8">
                <h1 class="text-xl font-bold text-gold">Racksephnox Admin</h1>
            </div>
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded-lg hover:bg-gold/10 transition">Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 rounded-lg hover:bg-gold/10 transition">Users</a>
                <a href="{{ route('admin.plans.index') }}" class="block px-4 py-2 rounded-lg hover:bg-gold/10 transition">Investment Plans</a>
                <a href="{{ route('admin.kyc.index') }}" class="block px-4 py-2 rounded-lg hover:bg-gold/10 transition">KYC Verification</a>
                <a href="{{ route('admin.deposits.index') }}" class="block px-4 py-2 rounded-lg hover:bg-gold/10 transition">Deposits</a>
                <a href="{{ route('admin.withdrawals.index') }}" class="block px-4 py-2 rounded-lg hover:bg-gold/10 transition">Withdrawals</a>
                <a href="{{ route('admin.reports.index') }}" class="block px-4 py-2 rounded-lg hover:bg-gold/10 transition">Reports</a>
                <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 rounded-lg hover:bg-gold/10 transition">Settings</a>
                <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded-lg hover:bg-gold/10 transition mt-4">← Back to Site</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <div class="bg-cosmic-deep border-b border-gold/30 px-6 py-4 flex justify-between items-center">
                <h2 class="text-gold">Welcome, {{ Auth::user()->name }}</h2>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gold-400 hover:text-gold">Logout</button>
                </form>
            </div>
            <div class="p-6">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
