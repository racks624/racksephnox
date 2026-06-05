<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Admin - Racksephnox</title>@vite(['resources/css/app.css'])</head>
<body class="bg-cosmic-void"><div class="flex"><div class="w-64 admin-sidebar p-4"><h2 class="text-gold text-xl">Admin Panel</h2><nav><a href="{{ route('admin.dashboard') }}" class="block py-2 text-gold-400">Dashboard</a><a href="{{ route('admin.users.index') }}" class="block py-2 text-gold-400">Users</a><a href="{{ route('admin.kyc.index') }}" class="block py-2 text-gold-400">KYC</a></nav></div><div class="flex-1 p-6">@yield('content')</div></div></body>
</html>
