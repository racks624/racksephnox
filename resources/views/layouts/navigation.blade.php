<nav x-data="{ open: false, scrolled: false }" x-init="() => { window.addEventListener('scroll', () => { scrolled = window.scrollY > 10 }); }"
    class="nav-golden fixed w-full top-0 z-50 transition-all duration-200" :class="{ 'shadow-lg backdrop-blur-md': scrolled }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
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
                    <x-nav-link :href="route('deposit.form')" :active="request()->routeIs('deposit.*')" class="nav-link-golden">
                        <i class="fas fa-arrow-down mr-1"></i> Deposit
                    </x-nav-link>
                    <x-nav-link :href="route('withdrawal.form')" :active="request()->routeIs('withdrawal.*')" class="nav-link-golden">
                        <i class="fas fa-arrow-up mr-1"></i> Withdraw
                    </x-nav-link>
                    <!-- Lottery Link -->
                    <x-nav-link :href="route('lottery.index')" :active="request()->routeIs('lottery.*')" class="nav-link-golden">
                        <i class="fas fa-dice-d6 mr-1"></i> Lottery
                    </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Right Side -->
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
                                <p class="text-sm">{{ $notification->data['message'] ?? 'New notification' }}</p>
                                <p class="text-xs text-gold-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            @endforeach
                            <div class="p-2 text-center border-t border-gold/30">
                                <a href="{{ route('notifications.index') }}" class="text-xs text-gold hover:text-gold-300">View all →</a>
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
                        <x-dropdown-link :href="route('wallet')" class="hover:bg-gold/10">
                            <i class="fas fa-wallet mr-2 text-gold"></i> My Wallet
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('machines.my-investments')" class="hover:bg-gold/10">
                            <i class="fas fa-microchip mr-2 text-gold"></i> My Investments
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('lottery.index')" class="hover:bg-gold/10">
                            <i class="fas fa-dice-d6 mr-2 text-gold"></i> Lottery
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
                    <button onclick="themeManager.toggleTheme()" class="p-2 rounded-full hover:bg-gold/10">
                        <i class="fas fa-palette text-gold"></i>
                    </button>
                </div>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-yellow-500 hover:bg-gray-700 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden bg-cosmic-deep/95 backdrop-blur-md border-t border-gold/20">
        <div class="pt-2 pb-3 space-y-1">
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
            <!-- Lottery Link (Mobile) -->
            <x-responsive-nav-link :href="route('lottery.index')" :active="request()->routeIs('lottery.*')" class="text-gold-400">
                <i class="fas fa-dice-d6 mr-2 w-5"></i> Lottery
            </x-responsive-nav-link>
        </div>

        @auth
        <div class="pt-4 pb-3 border-t border-gold/20">
            <div class="flex items-center px-4">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-gold-400 to-gold-600 flex items-center justify-center">
                        <i class="fas fa-user text-white"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-base font-medium text-white">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-medium text-gold-400">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-gold-400">
                    <i class="fas fa-user mr-2"></i> Profile
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('lottery.index')" class="text-gold-400">
                    <i class="fas fa-dice-d6 mr-2"></i> Lottery
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-400">
                        <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>

<!-- Prefetch critical routes for faster navigation -->
<script>
if ('requestIdleCallback' in window) {
    requestIdleCallback(() => {
        const links = ['{{ route("dashboard") }}', '{{ route("machines.index") }}', '{{ route("trading.index") }}', '{{ route("wallet") }}', '{{ route("lottery.index") }}'];
        links.forEach(url => {
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = url;
            document.head.appendChild(link);
        });
    });
}
</script>
