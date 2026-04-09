<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0a0a0f">
    <title>{{ config('app.name', 'Racksephnox') }} – Divine Golden Cryptocurrency Platform</title>
    
    <!-- Preload Critical Assets -->
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preload" as="style" href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap">
    
    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome (async loading) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    <!-- Theme Manager -->
    <script>
        (function() {
            // Immediately apply theme to avoid flash
            const savedTheme = localStorage.getItem('theme') || 'golden';
            document.documentElement.classList.add(savedTheme);
            
            window.themeManager = {
                currentTheme: savedTheme,
                themes: ['light', 'dark', 'cosmic', 'abundance', 'golden'],
                toggleTheme: function() {
                    let next = (this.themes.indexOf(this.currentTheme) + 1) % this.themes.length;
                    this.currentTheme = this.themes[next];
                    document.documentElement.classList.remove(...this.themes);
                    document.documentElement.classList.add(this.currentTheme);
                    localStorage.setItem('theme', this.currentTheme);
                    this.updateThemeIcon();
                },
                updateThemeIcon: function() {
                    const icon = document.querySelector('#theme-icon');
                    if (icon) {
                        const icons = { light: 'fa-sun', dark: 'fa-moon', cosmic: 'fa-star-of-life', abundance: 'fa-coins', golden: 'fa-crown' };
                        icon.className = `fas ${icons[this.currentTheme] || 'fa-palette'} text-gold`;
                    }
                }
            };
        })();
    </script>
</head>
<body class="font-sans antialiased bg-cosmic-void text-ivory">
    <div class="min-h-screen relative z-10">
        <!-- Golden Navigation -->
        <nav class="nav-golden fixed w-full top-0 z-50 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 hover:opacity-80 transition">
                                <img src="{{ asset('img/logo-racksephnox.svg') }}" alt="Racksephnox" class="h-10 w-auto" loading="eager">
                                <span class="text-xl font-bold golden-title hidden sm:inline">Racksephnox</span>
                            </a>
                        </div>

                        <!-- Desktop Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            @auth
                                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="nav-link-golden">
                                    <i class="fas fa-chart-line mr-1"></i> Dashboard
                                </x-nav-link>
                                <x-nav-link :href="route('machines.index')" :active="request()->routeIs('machines.*')" class="nav-link-golden">
                                    <i class="fas fa-microchip mr-1"></i> Machines
                                </x-nav-link>
                                <x-nav-link :href="route('trading.index')" :active="request()->routeIs('trading.*')" class="nav-link-golden">
                                    <i class="fab fa-bitcoin mr-1"></i> Trading
                                </x-nav-link>
                                <x-nav-link :href="route('social-trading.leaderboard')" :active="request()->routeIs('social-trading.*')" class="nav-link-golden">
                                    <i class="fas fa-trophy mr-1"></i> Leaderboard
                                </x-nav-link>
                                <x-nav-link :href="route('wallet')" :active="request()->routeIs('wallet')" class="nav-link-golden">
                                    <i class="fas fa-wallet mr-1"></i> Wallet
                                </x-nav-link>
                                <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')" class="nav-link-golden">
                                    <i class="fas fa-exchange-alt mr-1"></i> Transactions
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
                                <x-nav-link :href="route('kyc')" :active="request()->routeIs('kyc')" class="nav-link-golden">
                                    <i class="fas fa-id-card mr-1"></i> KYC
                                </x-nav-link>
                            @else
                                <x-nav-link :href="route('login')" :active="request()->routeIs('login')" class="nav-link-golden">
                                    <i class="fas fa-sign-in-alt mr-1"></i> Login
                                </x-nav-link>
                                <x-nav-link :href="route('register')" :active="request()->routeIs('register')" class="nav-link-golden">
                                    <i class="fas fa-user-plus mr-1"></i> Register
                                </x-nav-link>
                            @endauth
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        @auth
                            <!-- Notifications Bell -->
                            <div class="relative mr-3" x-data="{ open: false }">
                                <button @click="open = !open" class="relative p-2 rounded-full hover:bg-gold/10 transition" aria-label="Notifications">
                                    <i class="fas fa-bell text-gold-400 text-lg"></i>
                                    @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-gold-600 rounded-full transform translate-x-1/2 -translate-y-1/2">
                                            {{ $unreadNotificationsCount }}
                                        </span>
                                    @endif
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-cosmic-deep/95 backdrop-blur-md rounded-2xl shadow-golden border border-gold/30 z-20" style="display: none;">
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
                                    <x-dropdown-link href="#" @click="themeManager.toggleTheme(); return false;" class="hover:bg-gold/10">
                                        <i class="fas fa-palette mr-2 text-gold" id="theme-icon"></i> Switch Theme
                                    </x-dropdown-link>
                                    <div class="border-t border-gold/20 my-2"></div>
                                    <x-dropdown-link :href="route('lang.switch', 'en')" class="hover:bg-gold/10">
                                        🇬🇧 English
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('lang.switch', 'sw')" class="hover:bg-gold/10">
                                        🇰🇪 Kiswahili
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('lang.switch', 'fr')" class="hover:bg-gold/10">
                                        🇫🇷 Français
                                    </x-dropdown-link>
                                    <div class="border-t border-gold/20 my-2"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-400 hover:bg-red-500/10">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Log Out
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
                                <button onclick="window.themeManager.toggleTheme()" class="p-2 rounded-full hover:bg-gold/10 transition" aria-label="Switch Theme">
                                    <i class="fas fa-palette text-gold"></i>
                                </button>
                            </div>
                        @endauth
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="-me-2 flex items-center sm:hidden">
                        <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gold-400 hover:bg-gray-700 focus:outline-none transition" aria-label="Mobile Menu">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Panel -->
            <div x-show="open" @click.away="open = false" class="sm:hidden bg-cosmic-deep/95 backdrop-blur-md border-t border-gold/20" style="display: none;">
                <div class="pt-2 pb-3 space-y-1">
                    @auth
                        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-gold-400">
                            <i class="fas fa-chart-line mr-2 w-5"></i> Dashboard
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('machines.index')" :active="request()->routeIs('machines.*')" class="text-gold-400">
                            <i class="fas fa-microchip mr-2 w-5"></i> Machines
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('trading.index')" :active="request()->routeIs('trading.*')" class="text-gold-400">
                            <i class="fab fa-bitcoin mr-2 w-5"></i> Trading
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('social-trading.leaderboard')" :active="request()->routeIs('social-trading.*')" class="text-gold-400">
                            <i class="fas fa-trophy mr-2 w-5"></i> Leaderboard
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('wallet')" :active="request()->routeIs('wallet')" class="text-gold-400">
                            <i class="fas fa-wallet mr-2 w-5"></i> Wallet
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('deposit.form')" :active="request()->routeIs('deposit.*')" class="text-gold-400">
                            <i class="fas fa-arrow-down mr-2 w-5"></i> Deposit
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('withdrawal.form')" :active="request()->routeIs('withdrawal.*')" class="text-gold-400">
                            <i class="fas fa-arrow-up mr-2 w-5"></i> Withdraw
                        </x-responsive-nav-link>
                    @else
                        <x-responsive-nav-link :href="route('login')" :active="request()->routeIs('login')" class="text-gold-400">
                            <i class="fas fa-sign-in-alt mr-2 w-5"></i> Login
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('register')" :active="request()->routeIs('register')" class="text-gold-400">
                            <i class="fas fa-user-plus mr-2 w-5"></i> Register
                        </x-responsive-nav-link>
                    @endauth
                </div>
                
                @auth
                <div class="pt-4 pb-3 border-t border-gold/20">
                    <div class="px-4">
                        <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gold-400">{{ Auth::user()->email }}</div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link :href="route('profile.edit')" class="text-gold-400">
                            <i class="fas fa-user mr-2 w-5"></i> Profile
                        </x-responsive-nav-link>
                        @if(auth()->user()->is_admin)
                            <x-responsive-nav-link :href="route('admin.dashboard')" class="text-gold-400">
                                <i class="fas fa-tachometer-alt mr-2 w-5"></i> Admin
                            </x-responsive-nav-link>
                        @endif
                        <button @click="themeManager.toggleTheme()" class="w-full text-left px-4 py-2 text-gold-400 hover:bg-gold/10 transition">
                            <i class="fas fa-palette mr-2 w-5"></i> Switch Theme
                        </button>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-400">
                                <i class="fas fa-sign-out-alt mr-2 w-5"></i> Log Out
                            </x-responsive-nav-link>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
        </nav>

        <!-- Main Content -->
        <main class="pt-16">
            @yield('content')
        </main>
    </div>

    <!-- Sacred Footer -->
    <footer class="border-t border-gold/20 py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm text-gold-400 sacred-phrase">I Am The Source | Divine Golden Phi | Infinite Spiral of Creation</p>
            <p class="text-xs text-gold-500/60 mt-2">Guardian and Protector | Law of Information | Racksephnox</p>
            <p class="text-xs text-gold-500/40 mt-2">8888 Hz Wealth Frequency | Golden Ratio Φ | RX Machine Series</p>
        </div>
    </footer>

    <!-- Alpine.js (deferred) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Initialize Theme Icon -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.themeManager) {
                window.themeManager.updateThemeIcon();
            }
        });
    </script>
</body>
</html>
