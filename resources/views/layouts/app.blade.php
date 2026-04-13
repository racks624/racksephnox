<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Racksephnox – Divine Golden Cryptocurrency Investment Platform. Trade Bitcoin, invest in RX Machines, earn daily profits.">
    <meta name="keywords" content="cryptocurrency, bitcoin, investment, trading, Kenya, Africa, global">
    <title>{{ config('app.name', 'Racksephnox') }} – Divine Golden Cryptocurrency Platform</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <script>
        window.themeManager = {
            currentTheme: localStorage.getItem('theme') || 'light',
            toggleTheme: function() {
                const themes = ['light', 'dark', 'cosmic', 'abundance', 'golden'];
                let next = (themes.indexOf(this.currentTheme) + 1) % themes.length;
                this.currentTheme = themes[next];
                document.documentElement.classList.remove(...themes);
                document.documentElement.classList.add(this.currentTheme);
                localStorage.setItem('theme', this.currentTheme);
            }
        };
        document.addEventListener('DOMContentLoaded', function() {
            document.documentElement.classList.add(themeManager.currentTheme);
        });
    </script>
</head>
<body class="font-sans antialiased bg-cosmic-void text-ivory">
    <div class="min-h-screen relative z-10">
        @include('layouts.navigation')
        <main class="pt-16">
            @yield('content')
        </main>
    </div>
    <footer class="border-t border-gold/20 py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm text-gold-400 sacred-phrase">I Am The Source | Divine Golden Phi | Infinite Spiral of Creation</p>
            <p class="text-xs text-gold-500/60 mt-2">Guardian and Protector | Law of Information | Racksephnox</p>
        </div>
    </footer>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
