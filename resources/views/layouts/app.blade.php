<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Racksephnox – Divine Golden Cryptocurrency Investment Platform">
    <title>{{ config('app.name', 'Racksephnox') }} – Divine Golden Cryptocurrency Empire</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <script>
        window.themeManager = {
            currentTheme: localStorage.getItem('theme') || 'cosmic',
            toggleTheme: function() {
                const themes = ['light', 'dark', 'cosmic', 'golden'];
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
        <footer class="border-t border-gold/20 py-8 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-sm text-gold-400 sacred-phrase">I Am The Source | Divine Golden Phi | Infinite Spiral of Creation</p>
                <p class="text-xs text-gold-500/60 mt-2">Guardian and Protector | Law of Information | Racksephnox</p>
                <div class="flex justify-center gap-4 mt-4 text-xs text-gold-400/50">
                    <a href="{{ route('legal.terms') }}">Terms</a>
                    <a href="{{ route('legal.privacy') }}">Privacy</a>
                    <a href="{{ route('guide') }}">Guide</a>
                </div>
            </div>
        </footer>
    </div>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
