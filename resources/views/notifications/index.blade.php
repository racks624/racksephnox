@extends('layouts.app')

@section('content')
<div x-data="notificationsManager()" x-init="init()" class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <!-- Header -->
            <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gold">✨ Divine Notifications</h1>
                    <p class="text-xs text-gold-400/70 mt-1">Stay connected with your cosmic journey</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('notifications.preferences') }}" class="btn-golden text-sm">
                        <i class="fas fa-cog mr-1"></i> Preferences
                    </a>
                    <button @click="markAllRead()" class="btn-golden text-sm">
                        <i class="fas fa-check-double mr-1"></i> Mark All Read
                    </button>
                    <button @click="clearAll()" class="btn-golden text-sm bg-red-600 hover:bg-red-700">
                        <i class="fas fa-trash-alt mr-1"></i> Clear All
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                <div class="bg-gold/10 rounded-xl p-3 text-center">
                    <p class="text-xs text-ivory/50">Total</p>
                    <p class="text-2xl font-bold text-gold">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-gold/10 rounded-xl p-3 text-center">
                    <p class="text-xs text-ivory/50">Unread</p>
                    <p class="text-2xl font-bold text-yellow-400">{{ $stats['unread'] }}</p>
                </div>
                <div class="bg-gold/10 rounded-xl p-3 text-center">
                    <p class="text-xs text-ivory/50">Read</p>
                    <p class="text-2xl font-bold text-green-400">{{ $stats['read'] }}</p>
                </div>
                <div class="bg-gold/10 rounded-xl p-3 text-center">
                    <p class="text-xs text-ivory/50">Today</p>
                    <p class="text-2xl font-bold text-gold">{{ $notifications->where('created_at', '>=', now()->startOfDay())->count() }}</p>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex flex-wrap border-b border-gold/30 mb-6">
                <a href="{{ route('notifications.index') }}" class="px-4 py-2 {{ !request('type') && !request('category') ? 'border-b-2 border-gold text-gold' : 'text-ivory/70 hover:text-gold' }}">
                    <i class="fas fa-bell mr-1"></i> All
                </a>
                <a href="{{ route('notifications.index', ['type' => 'unread']) }}" class="px-4 py-2 {{ request('type') === 'unread' ? 'border-b-2 border-gold text-gold' : 'text-ivory/70 hover:text-gold' }}">
                    <i class="fas fa-circle mr-1"></i> Unread
                    @if($stats['unread'] > 0)
                        <span class="ml-1 text-xs bg-red-500 text-white rounded-full px-1.5 py-0.5">{{ $stats['unread'] }}</span>
                    @endif
                </a>
                <a href="{{ route('notifications.index', ['type' => 'read']) }}" class="px-4 py-2 {{ request('type') === 'read' ? 'border-b-2 border-gold text-gold' : 'text-ivory/70 hover:text-gold' }}">
                    <i class="fas fa-check-circle mr-1"></i> Read
                </a>
                <a href="{{ route('notifications.index', ['category' => 'deposit']) }}" class="px-4 py-2 {{ request('category') === 'deposit' ? 'border-b-2 border-gold text-gold' : 'text-ivory/70 hover:text-gold' }}">
                    <i class="fas fa-arrow-down mr-1"></i> Deposits
                </a>
                <a href="{{ route('notifications.index', ['category' => 'investment']) }}" class="px-4 py-2 {{ request('category') === 'investment' ? 'border-b-2 border-gold text-gold' : 'text-ivory/70 hover:text-gold' }}">
                    <i class="fas fa-chart-line mr-1"></i> Investments
                </a>
                <a href="{{ route('notifications.index', ['category' => 'withdrawal']) }}" class="px-4 py-2 {{ request('category') === 'withdrawal' ? 'border-b-2 border-gold text-gold' : 'text-ivory/70 hover:text-gold' }}">
                    <i class="fas fa-arrow-up mr-1"></i> Withdrawals
                </a>
                <a href="{{ route('notifications.index', ['category' => 'trading']) }}" class="px-4 py-2 {{ request('category') === 'trading' ? 'border-b-2 border-gold text-gold' : 'text-ivory/70 hover:text-gold' }}">
                    <i class="fab fa-bitcoin mr-1"></i> Trading
                </a>
            </div>

            <!-- Notifications List -->
            @if($notifications->count())
                <div class="space-y-2">
                    @foreach($notifications->groupBy(function($n) { return $n->created_at->format('Y-m-d'); }) as $date => $group)
                        <div class="text-xs text-gold-400/50 mt-4 mb-2">{{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</div>
                        @foreach($group as $notification)
                        <div class="group bg-gold/5 rounded-lg p-4 transition-all hover:bg-gold/10 {{ $notification->read_at ? 'opacity-70' : 'border-l-4 border-gold' }}" data-id="{{ $notification->id }}">
                            <div class="flex justify-between items-start">
                                <div class="flex gap-3 flex-1">
                                    <span class="text-2xl">{{ $notification->data['icon'] ?? '🔔' }}</span>
                                    <div class="flex-1">
                                        <p class="text-ivory">{{ $notification->data['message'] ?? 'New notification' }}</p>
                                        <div class="flex flex-wrap gap-3 mt-1">
                                            <p class="text-xs text-gold-400">{{ $notification->created_at->diffForHumans() }}</p>
                                            @if(isset($notification->data['category']))
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-gold/20 text-gold-400">
                                                    {{ ucfirst($notification->data['category']) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
                                    @if(!$notification->read_at)
                                        <button @click="markAsRead('{{ $notification->id }}')" class="text-xs text-gold-400 hover:text-gold" title="Mark as read">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    <button @click="deleteNotification('{{ $notification->id }}')" class="text-xs text-red-400 hover:text-red-300" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endforeach
                </div>
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-bell-slash text-5xl text-gold/30 mb-4"></i>
                    <p class="text-ivory/50">✨ No notifications yet. Your cosmic journey awaits.</p>
                    <p class="text-xs text-gold-400/50 mt-2">When you receive notifications, they will appear here</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function notificationsManager() {
    return {
        async init() {
            // Auto-refresh notifications every 30 seconds
            setInterval(() => this.refreshUnreadCount(), 30000);
        },
        
        async markAsRead(id) {
            try {
                const response = await fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                if (response.ok) {
                    location.reload();
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        },
        
        async markAllRead() {
            if (!confirm('Mark all notifications as read?')) return;
            try {
                const response = await fetch('{{ route("notifications.markAllRead") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                if (response.ok) {
                    location.reload();
                }
            } catch (error) {
                console.error('Error marking all as read:', error);
            }
        },
        
        async deleteNotification(id) {
            if (!confirm('Delete this notification?')) return;
            try {
                const response = await fetch(`/notifications/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                if (response.ok) {
                    location.reload();
                }
            } catch (error) {
                console.error('Error deleting notification:', error);
            }
        },
        
        async clearAll() {
            if (!confirm('Delete all notifications? This action cannot be undone.')) return;
            try {
                const response = await fetch('{{ route("notifications.destroyAll") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                if (response.ok) {
                    location.reload();
                }
            } catch (error) {
                console.error('Error clearing notifications:', error);
            }
        },
        
        async refreshUnreadCount() {
            try {
                const response = await fetch('/api/notifications/unread-count');
                const data = await response.json();
                if (data.count !== {{ $stats['unread'] }}) {
                    location.reload();
                }
            } catch (error) {
                console.error('Error refreshing unread count:', error);
            }
        }
    }
}
</script>
@endsection
