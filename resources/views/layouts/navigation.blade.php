<nav x-data="{ open: false }" class="nav-golden fixed w-full top-0 z-50 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo Section -->
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 group">
                        <div class="w-10 h-10 bg-gradient-to-r from-gold-400 to-gold-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-all duration-300">
                            <span class="text-xl font-black text-white">R</span>
                        </div>
                        <span class="text-xl font-bold golden-title hidden sm:inline group-hover:shimmer-gold transition">Racksephnox</span>
                    </a>
                </div>

                <!-- Desktop Navigation Links (Left Side) -->
                <div class="hidden space-x-1 sm:-my-px sm:ml-10 sm:flex sm:items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="nav-link-golden px-3 py-2 rounded-lg transition-all duration-300 hover:scale-105">
                        <i class="fas fa-chart-line mr-1"></i> Dashboard
                    </x-nav-link>
                    <x-nav-link :href="route('machines.index')" :active="request()->routeIs('machines.*')" class="nav-link-golden px-3 py-2 rounded-lg transition-all duration-300 hover:scale-105">
                        <i class="fas fa-microchip mr-1"></i> Machines
                    </x-nav-link>
                    <x-nav-link :href="route('trading.index')" :active="request()->routeIs('trading.*')" class="nav-link-golden px-3 py-2 rounded-lg transition-all duration-300 hover:scale-105">
                        <i class="fab fa-bitcoin mr-1"></i> Trading
                    </x-nav-link>
                    <x-nav-link :href="route('social-trading.leaderboard')" :active="request()->routeIs('social-trading.*')" class="nav-link-golden px-3 py-2 rounded-lg transition-all duration-300 hover:scale-105">
                        <i class="fas fa-trophy mr-1"></i> Leaderboard
                    </x-nav-link>
                    <x-nav-link :href="route('wallet')" :active="request()->routeIs('wallet')" class="nav-link-golden px-3 py-2 rounded-lg transition-all duration-300 hover:scale-105">
                        <i class="fas fa-wallet mr-1"></i> Wallet
                    </x-nav-link>
                    <x-nav-link :href="route('deposit.form')" :active="request()->routeIs('deposit.*')" class="nav-link-golden px-3 py-2 rounded-lg transition-all duration-300 hover:scale-105">
                        <i class="fas fa-arrow-down mr-1"></i> Deposit
                    </x-nav-link>
                    <x-nav-link :href="route('withdrawal.form')" :active="request()->routeIs('withdrawal.*')" class="nav-link-golden px-3 py-2 rounded-lg transition-all duration-300 hover:scale-105">
                        <i class="fas fa-arrow-up mr-1"></i> Withdraw
                    </x-nav-link>
                </div>
            </div>

            <!-- Right Side Navigation (User Menu) -->
            <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-3">
                @auth
                    <!-- Live BTC Price Ticker -->
                    <div class="hidden lg:flex items-center gap-2 bg-gold/10 rounded-full px-3 py-1.5 border border-gold/30">
                        <i class="fab fa-bitcoin text-gold text-sm animate-pulse"></i>
                        <span class="text-xs text-ivory">BTC</span>
                        <span class="text-xs text-gold font-mono" id="navBtcPrice">$0</span>
                        <span class="text-xs text-green-400" id="navBtcChange">+2.4%</span>
                    </div>

                    <!-- Notifications Bell with Badge -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="relative p-2 rounded-full hover:bg-gold/10 transition-all duration-300 group">
                            <i class="fas fa-bell text-gold-400 text-lg group-hover:scale-110 transition"></i>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px] animate-pulse" 
                                  x-show="unreadCount > 0" x-text="unreadCount"></span>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-cosmic-deep/95 backdrop-blur-md rounded-2xl shadow-2xl border border-gold/30 z-50">
                            <div class="p-3 border-b border-gold/30 font-semibold text-gold flex justify-between">
                                <span>Notifications</span>
                                <button @click="markAllRead" class="text-xs text-gold-400 hover:text-gold">Mark all read</button>
                            </div>
                            <div class="max-h-96 overflow-y-auto custom-scroll">
                                @if(isset($latestNotifications) && $latestNotifications->count())
                                    @foreach($latestNotifications as $notification)
                                    <div class="p-3 border-b border-gold/20 hover:bg-gold/5 transition cursor-pointer {{ $notification->read_at ? '' : 'bg-gold/5 border-l-4 border-l-gold' }}">
                                        <p class="text-sm text-ivory">{{ $notification->data['message'] ?? 'New notification' }}</p>
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

                    <!-- User Dropdown with Avatar -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 px-3 py-2 border border-gold/30 rounded-full text-sm leading-4 font-medium text-ivory bg-cosmic-deep/50 hover:bg-gold/10 transition-all duration-300 group">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-6 h-6 rounded-full object-cover">
                                @else
                                    <div class="w-6 h-6 rounded-full bg-gradient-to-r from-gold-400 to-gold-600 flex items-center justify-center">
                                        <i class="fas fa-user text-xs text-white"></i>
                                    </div>
                                @endif
                                <div class="hidden md:block">{{ Auth::user()->name }}</div>
                                <i class="fas fa-chevron-down text-gold text-xs group-hover:rotate-180 transition-all duration-300"></i>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- User Info Header -->
                            <div class="px-4 py-3 border-b border-gold/20">
                                <div class="font-medium text-ivory">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-gold-400">{{ Auth::user()->email }}</div>
                                <div class="text-xs text-gold-500 mt-1">Balance: KES {{ number_format(Auth::user()->wallet->balance ?? 0, 2) }}</div>
                            </div>
                            
                            <x-dropdown-link :href="route('profile.edit')" class="hover:bg-gold/10 transition">
                                <i class="fas fa-user mr-2 text-gold"></i> My Profile
                            </x-dropdown-link>
                            
                            <x-dropdown-link :href="route('wallet')" class="hover:bg-gold/10 transition">
                                <i class="fas fa-wallet mr-2 text-gold"></i> My Wallet
                            </x-dropdown-link>
                            
                            <x-dropdown-link :href="route('machines.my-investments')" class="hover:bg-gold/10 transition">
                                <i class="fas fa-microchip mr-2 text-gold"></i> My Investments
                            </x-dropdown-link>
                            
                            <x-dropdown-link :href="route('transactions.index')" class="hover:bg-gold/10 transition">
                                <i class="fas fa-exchange-alt mr-2 text-gold"></i> Transactions
                            </x-dropdown-link>
                            
                            <x-dropdown-link :href="route('referrals')" class="hover:bg-gold/10 transition">
                                <i class="fas fa-users mr-2 text-gold"></i> Referrals
                            </x-dropdown-link>
                            
                            @if(auth()->user()->is_admin)
                                <div class="border-t border-gold/20 my-1"></div>
                                <x-dropdown-link :href="route('admin.dashboard')" class="hover:bg-gold/10 transition">
                                    <i class="fas fa-tachometer-alt mr-2 text-gold"></i> Admin Dashboard
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.users.index')" class="hover:bg-gold/10 transition">
                                    <i class="fas fa-users mr-2 text-gold"></i> Manage Users
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.deposits.index')" class="hover:bg-gold/10 transition">
                                    <i class="fas fa-arrow-down mr-2 text-gold"></i> Verify Deposits
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.withdrawals.index')" class="hover:bg-gold/10 transition">
                                    <i class="fas fa-arrow-up mr-2 text-gold"></i> Process Withdrawals
                                </x-dropdown-link>
                            @endif
                            
                            <div class="border-t border-gold/20 my-1"></div>
                            
                            <x-dropdown-link href="#" @click="themeManager.toggleTheme(); return false;" class="hover:bg-gold/10 transition">
                                <i class="fas fa-palette mr-2 text-gold"></i> Switch Theme
                            </x-dropdown-link>
                            
                            <x-dropdown-link href="{{ route('lang.switch', 'en') }}" class="hover:bg-gold/10 transition">
                                🇬🇧 English
                            </x-dropdown-link>
                            <x-dropdown-link href="{{ route('lang.switch', 'sw') }}" class="hover:bg-gold/10 transition">
                                🇰🇪 Kiswahili
                            </x-dropdown-link>
                            
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="hover:bg-red-500/10 text-red-400 transition">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('login') }}" class="text-ivory hover:text-gold transition px-3 py-2 rounded-lg hover:bg-gold/10">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="btn-golden text-sm py-2 px-4 hover:scale-105 transition">
                            <i class="fas fa-user-plus mr-1"></i> Get Started
                        </a>
                        <button onclick="themeManager.toggleTheme()" class="p-2 rounded-full hover:bg-gold/10 transition">
                            <i class="fas fa-palette text-gold"></i>
                        </button>
                    </div>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gold-400 hover:text-gold hover:bg-gold/10 focus:outline-none transition-all duration-300">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden bg-cosmic-deep/95 backdrop-blur-md border-t border-gold/20">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-gold-400 block px-4 py-2 hover:bg-gold/10">
                <i class="fas fa-chart-line mr-2 w-5"></i> Dashboard
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('machines.index')" :active="request()->routeIs('machines.*')" class="text-gold-400 block px-4 py-2 hover:bg-gold/10">
                <i class="fas fa-microchip mr-2 w-5"></i> Machines
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('trading.index')" :active="request()->routeIs('trading.*')" class="text-gold-400 block px-4 py-2 hover:bg-gold/10">
                <i class="fab fa-bitcoin mr-2 w-5"></i> Trading
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('social-trading.leaderboard')" :active="request()->routeIs('social-trading.*')" class="text-gold-400 block px-4 py-2 hover:bg-gold/10">
                <i class="fas fa-trophy mr-2 w-5"></i> Leaderboard
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('wallet')" :active="request()->routeIs('wallet')" class="text-gold-400 block px-4 py-2 hover:bg-gold/10">
                <i class="fas fa-wallet mr-2 w-5"></i> Wallet
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('deposit.form')" :active="request()->routeIs('deposit.*')" class="text-gold-400 block px-4 py-2 hover:bg-gold/10">
                <i class="fas fa-arrow-down mr-2 w-5"></i> Deposit
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('withdrawal.form')" :active="request()->routeIs('withdrawal.*')" class="text-gold-400 block px-4 py-2 hover:bg-gold/10">
                <i class="fas fa-arrow-up mr-2 w-5"></i> Withdraw
            </x-responsive-nav-link>
        </div>
        
        @auth
        <div class="pt-4 pb-3 border-t border-gold/20">
            <div class="flex items-center px-4">
                <div class="flex-shrink-0">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="h-10 w-10 rounded-full object-cover">
                    @else
                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-gold-400 to-gold-600 flex items-center justify-center">
                            <i class="fas fa-user text-white"></i>
                        </div>
                    @endif
                </div>
                <div class="ml-3">
                    <div class="text-base font-medium text-white">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-medium text-gold-400">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-gold-400 block px-4 py-2 hover:bg-gold/10">
                    <i class="fas fa-user mr-2"></i> Profile
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('notifications.index')" class="text-gold-400 block px-4 py-2 hover:bg-gold/10">
                    <i class="fas fa-bell mr-2"></i> Notifications
                    <span class="ml-1 inline-block w-5 h-5 text-center text-xs bg-red-500 text-white rounded-full" x-show="unreadCountMobile > 0" x-text="unreadCountMobile"></span>
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-400 block px-4 py-2 hover:bg-red-500/10">
                        <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>

<script>
// Live BTC price for navigation
function updateNavBtcPrice() {
    fetch('/api/trading/price')
        .then(response => response.json())
        .then(data => {
            const priceElement = document.getElementById('navBtcPrice');
            if (priceElement && data.price_kes) {
                priceElement.innerText = 'KES ' + (data.price_kes / 130).toFixed(0);
            }
        })
        .catch(error => console.error('Failed to fetch BTC price:', error));
}

// Update unread count for mobile
let unreadCountMobile = {{ $unreadNotificationsCount ?? 0 }};

// Refresh unread count periodically
setInterval(() => {
    fetch('/api/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            unreadCountMobile = data.count;
        })
        .catch(error => console.error('Failed to fetch unread count:', error));
}, 30000);

updateNavBtcPrice();
setInterval(updateNavBtcPrice, 30000);
</script>
