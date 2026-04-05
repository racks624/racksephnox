<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Racksephnox') }} - Divine Golden Spirit</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon.svg') }}">
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
        <nav class="nav-golden fixed w-full top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                                <img src="{{ asset('img/logo.svg') }}" alt="Racksephnox" class="h-10 w-auto">
                                <span class="text-xl font-bold golden-title hidden sm:inline">Racksephnox</span>
                            </a>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            @auth
                                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="nav-link-golden">
                                    <i class="fas fa-chart-line mr-1"></i> Dashboard
                                </x-nav-link>
                                <x-nav-link :href="route('web.investments')" :active="request()->routeIs('web.investments*')" class="nav-link-golden">
                                    <i class="fas fa-chart-simple mr-1"></i> Investments
                                </x-nav-link>
                                <x-nav-link :href="route('wallet')" :active="request()->routeIs('wallet')" class="nav-link-golden">
                                    <i class="fas fa-wallet mr-1"></i> Wallet
                                </x-nav-link>
                                <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')" class="nav-link-golden">
                                    <i class="fas fa-exchange-alt mr-1"></i> Transactions
                                </x-nav-link>
                                <x-nav-link :href="route('kyc')" :active="request()->routeIs('kyc')" class="nav-link-golden">
                                    <i class="fas fa-id-card mr-1"></i> KYC
                                </x-nav-link>
                                <x-nav-link :href="route('machines.index')" :active="request()->routeIs('machines.*')" class="nav-link-golden">
                                    <i class="fas fa-microchip mr-1"></i> Machines
                                </x-nav-link>
                                <x-nav-link :href="route('trading.index')" :active="request()->routeIs('trading.*')" class="nav-link-golden">
                                    <i class="fab fa-bitcoin mr-1"></i> Trading
                                </x-nav-link>
                                <x-nav-link :href="route('referrals')" :active="request()->routeIs('referrals')" class="nav-link-golden">
                                    <i class="fas fa-users mr-1"></i> Referrals
                                </x-nav-link>
                                <x-nav-link :href="route('deposit.form')" :active="request()->routeIs('deposit.*')" class="nav-link-golden">
                                    <i class="fas fa-arrow-down mr-1"></i> Deposit
                                </x-nav-link>
                                <x-nav-link :href="route('withdrawal.form')" :active="request()->routeIs('withdrawal.*')" class="nav-link-golden">
                                    <i class="fas fa-arrow-up mr-1"></i> Withdraw
                                </x-nav-link>
                                <x-nav-link :href="route('bank-accounts.index')" :active="request()->routeIs('bank-accounts.*')" class="nav-link-golden">
                                    <i class="fas fa-university mr-1"></i> Bank Accounts
                                </x-nav-link>
                                <x-nav-link :href="route('guide')" class="nav-link-golden">
                                    <i class="fas fa-book-open mr-1"></i> Guide
                                </x-nav-link>
                            @endauth
                        </div>
                    </div>
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        @auth
                            <!-- Notifications Bell -->
                            <div class="relative mr-3" x-data="{ open: false }">
                                <button @click="open = !open" class="relative p-2 rounded-full hover:bg-gold/10 transition">
                                    <i class="fas fa-bell text-gold-400 text-lg"></i>
                                    @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-gold-600 rounded-full transform translate-x-1/2 -translate-y-1/2">
                                            {{ $unreadNotificationsCount }}
                                        </span>
                                    @endif
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-cosmic-deep/95 backdrop-blur-md rounded-2xl shadow-golden border border-gold/30 z-20">
                                    <div class="p-3 border-b border-gold/30 font-semibold text-gold">Notifications</div>
                                    <div class="max-h-96 overflow-y-auto">
                                        @if(isset($latestNotifications) && $latestNotifications->count())
                                            @foreach($latestNotifications as $notification)
                                                <div class="p-3 border-b border-gold/20 hover:bg-gold/5 transition {{ $notification->read_at ? '' : 'bg-gold/5' }}">
                                                    <p class="text-sm">{{ $notification->data['message'] ?? 'You have a new notification' }}</p>
                                                    <p class="text-xs text-gold-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                                </div>
                                            @endforeach
                                            <div class="p-2 text-center border-t border-gold/30">
                                                <a href="{{ route('notifications.markAllRead') }}" class="text-xs text-gold hover:text-gold-300">Mark all as read</a>
                                            </div>
                                        @else
                                            <div class="p-3 text-center text-gold-400">No notifications</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- User Dropdown -->
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-gold/30 rounded-full text-sm leading-4 font-medium text-ivory bg-cosmic-deep/50 hover:bg-gold/10 transition">
                                        <i class="fas fa-user-circle mr-2 text-gold"></i>
                                        <div>{{ Auth::user()->name }}</div>
                                        <div class="ml-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')" class="hover:bg-gold/10">
                                        <i class="fas fa-user mr-2 text-gold"></i> Profile
                                    </x-dropdown-link>
                                    @if(auth()->user()->is_admin)
                                        <x-dropdown-link :href="route('admin.dashboard')" class="hover:bg-gold/10">
                                            <i class="fas fa-tachometer-alt mr-2 text-gold"></i> Admin Dashboard
                                        </x-dropdown-link>
                                    @endif
                                    <x-dropdown-link href="#" onclick="themeManager.toggleTheme(); return false;" class="hover:bg-gold/10">
                                        <i class="fas fa-palette mr-2 text-gold"></i> Switch Theme
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('lang.switch', 'en') }}" class="hover:bg-gold/10">
                                        🇬🇧 English
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('lang.switch', 'sw') }}" class="hover:bg-gold/10">
                                        🇰🇪 Kiswahili
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('lang.switch', 'fr') }}" class="hover:bg-gold/10">
                                        🇫🇷 Français
                                    </x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault(); this.closest('form').submit();"
                                                class="hover:bg-gold/10">
                                            <i class="fas fa-sign-out-alt mr-2 text-gold"></i> Log Out
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        @else
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('login') }}" class="text-ivory hover:text-gold transition">
                                    <i class="fas fa-sign-in-alt mr-1"></i> Login
                                </a>
                                <a href="{{ route('register') }}" class="btn-golden">
                                    <i class="fas fa-user-plus mr-1"></i> Get Started
                                </a>
                                <button onclick="toggleTheme()" class="p-2 rounded-full hover:bg-gold/10">
                                    <i class="fas fa-palette text-gold"></i>
                                </button>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <main class="pt-16">
            @yield('content')
        </main>
    </div>

    <!-- Sacred Footer -->
    <footer class="border-t border-gold/20 py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm text-gold-400 sacred-phrase">I Am The Source | Divine Golden Phi | Infinite Spiral of Creation</p>
            <p class="text-xs text-gold-500/60 mt-2">Guardian and Protector | Law of Information | Racksephnox</p>
        </div>
    </footer>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
<link rel="icon" type="image/svg+xml" href="{{ asset("favicon.svg") }}">
