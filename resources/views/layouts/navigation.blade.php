<nav x-data="{ open: false }" class="nav-golden fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <img src="{{ asset('img/logo-racksephnox.svg') }}" alt="Racksephnox" class="h-10 w-auto">
                        <span class="text-xl font-bold golden-title hidden sm:inline">Racksephnox</span>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="nav-link-golden">
                        <i class="fas fa-chart-line mr-1"></i> Dashboard
                    </x-nav-link>
                    <x-nav-link :href="route('machines.index')" :active="request()->routeIs('machines.*')" class="nav-link-golden">
                        <i class="fas fa-microchip mr-1"></i> Machines
                    </x-nav-link>
                    <x-nav-link :href="route('trading.index')" :active="request()->routeIs('trading.*')" class="nav-link-golden">
                        <i class="fab fa-bitcoin mr-1"></i> Trading
                    </x-nav-link>
                    <x-nav-link :href="route('wallet')" :active="request()->routeIs('wallet')" class="nav-link-golden">
                        <i class="fas fa-wallet mr-1"></i> Wallet
                    </x-nav-link>
                    <x-nav-link :href="route('social-trading.leaderboard')" :active="request()->routeIs('social-trading.*')" class="nav-link-golden">
                        <i class="fas fa-trophy mr-1"></i> Leaderboard
                    </x-nav-link>
                    <x-nav-link :href="route('deposit.form')" :active="request()->routeIs('deposit.*')" class="nav-link-golden">
                        <i class="fas fa-arrow-down mr-1"></i> Deposit
                    </x-nav-link>
                    <x-nav-link :href="route('withdrawal.form')" :active="request()->routeIs('withdrawal.*')" class="nav-link-golden">
                        <i class="fas fa-arrow-up mr-1"></i> Withdraw
                    </x-nav-link>
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
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="hover:bg-gold/10">
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
                    </div>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-yellow-500 hover:bg-gray-700 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-cosmic-deep/95">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-gold-400">
                <i class="fas fa-chart-line mr-2"></i> Dashboard
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('machines.index')" :active="request()->routeIs('machines.*')" class="text-gold-400">
                <i class="fas fa-microchip mr-2"></i> Machines
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('trading.index')" :active="request()->routeIs('trading.*')" class="text-gold-400">
                <i class="fab fa-bitcoin mr-2"></i> Trading
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('social-trading.leaderboard')" :active="request()->routeIs('social-trading.*')" class="text-gold-400">
                <i class="fas fa-trophy mr-2"></i> Leaderboard
            </x-responsive-nav-link>
        </div>
        <div class="pt-4 pb-1 border-t border-gold/20">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name ?? 'Guest' }}</div>
                <div class="font-medium text-sm text-gold-400">{{ Auth::user()->email ?? '' }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-gold-400">
                    <i class="fas fa-user mr-2"></i> Profile
                </x-responsive-nav-link>
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-400">
                            <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                        </x-responsive-nav-link>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>
