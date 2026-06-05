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
        /* Alternative admin styles – collapsible sidebar + top navbar */
        .alt-admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        .alt-sidebar {
            width: 260px;
            background: linear-gradient(180deg, #0F172A 0%, #1E1B2E 100%);
            border-right: 1px solid rgba(212, 175, 55, 0.2);
            transition: all 0.3s;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 40;
            overflow-y: auto;
        }
        .alt-sidebar.collapsed {
            width: 80px;
        }
        .alt-sidebar.collapsed .sidebar-text,
        .alt-sidebar.collapsed .sidebar-icon-only {
            display: none;
        }
        .alt-sidebar.collapsed .sidebar-icon {
            margin-right: 0;
        }
        .alt-main-content {
            flex: 1;
            margin-left: 260px;
            transition: margin-left 0.3s;
        }
        .alt-sidebar.collapsed ~ .alt-main-content {
            margin-left: 80px;
        }
        .alt-topbar {
            background: rgba(15, 25, 35, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(212, 175, 55, 0.3);
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 30;
        }
        .alt-card {
            background: rgba(15, 25, 35, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 1rem;
            transition: all 0.2s;
        }
        .alt-card:hover {
            border-color: rgba(212, 175, 55, 0.6);
            transform: translateY(-2px);
        }
        .scrollbar-thin::-webkit-scrollbar { width: 4px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: #1e293b; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: #D4AF37; border-radius: 4px; }
        .btn-golden {
            background: linear-gradient(135deg, #D4AF37, #B8860B);
            color: #0F172A;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            transition: all 0.2s;
        }
        .btn-golden:hover { transform: scale(1.02); filter: brightness(1.1); }
    </style>
</head>
<body class="bg-cosmic-void text-ivory font-sans antialiased">
    <div class="alt-admin-wrapper">
        <!-- Sidebar -->
        @include('admin.partials.alt-nav')

        <!-- Main Content Area -->
        <div class="alt-main-content">
            <!-- Top Bar -->
            <div class="alt-topbar">
                <div class="flex items-center gap-3">
                    <button id="sidebarToggle" class="text-gold-400 hover:text-gold text-xl">
                        <i class="fas fa-bars"></i>
                    </button>
                    <span class="text-sm text-gold-400">Live: {{ now()->format('H:i:s') }}</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 rounded-full border border-gold/30 hover:bg-gold/10 transition">
                            <div class="w-6 h-6 rounded-full bg-gold/30 flex items-center justify-center">
                                <i class="fas fa-user text-gold text-xs"></i>
                            </div>
                            <span class="text-sm">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs text-gold-400"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-cosmic-deep/95 backdrop-blur rounded-xl border border-gold/30 shadow-xl z-50">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-gold/10">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-gold/10 text-red-400">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main class="p-6 scrollbar-thin">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Sidebar toggle with local storage
        const sidebar = document.querySelector('.alt-sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        if (localStorage.getItem('adminSidebarCollapsed') === 'true') {
            sidebar.classList.add('collapsed');
        }
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('adminSidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
    </script>
    @stack('scripts')
</body>
</html>
