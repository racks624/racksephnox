@extends('layouts.app')

@section('content')
<div x-data="notificationsManager()" x-init="init()" class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
                <h1 class="text-2xl font-bold text-gold">✨ Divine Notifications</h1>
                <div class="flex gap-2">
                    <a href="{{ route('notifications.preferences') }}" class="btn-golden text-sm">⚙️ Preferences</a>
                    <form action="{{ route('notifications.markAllRead') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn-golden text-sm">📖 Mark All Read</button>
                    </form>
                    <form action="{{ route('notifications.destroyAll') }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-golden text-sm bg-red-600 hover:bg-red-700">🗑️ Clear All</button>
                    </form>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-gold/30 mb-6">
                <a href="{{ route('notifications.index') }}" class="px-4 py-2 {{ !request('type') ? 'border-b-2 border-gold text-gold' : 'text-ivory/70' }}">All</a>
                <a href="{{ route('notifications.index', ['type' => 'unread']) }}" class="px-4 py-2 {{ request('type') === 'unread' ? 'border-b-2 border-gold text-gold' : 'text-ivory/70' }}">Unread</a>
                <a href="{{ route('notifications.index', ['type' => 'read']) }}" class="px-4 py-2 {{ request('type') === 'read' ? 'border-b-2 border-gold text-gold' : 'text-ivory/70' }}">Read</a>
            </div>

            <!-- Notifications List -->
            @if($notifications->count())
                <div class="space-y-3">
                    @foreach($notifications as $notification)
                    <div class="bg-gold/5 rounded-lg p-4 transition-all hover:bg-gold/10 {{ $notification->read_at ? 'opacity-70' : 'border-l-4 border-gold' }}">
                        <div class="flex justify-between items-start">
                            <div class="flex gap-3">
                                <span class="text-2xl">{{ $notification->data['icon'] ?? '🔔' }}</span>
                                <div>
                                    <p class="text-ivory">{{ $notification->data['message'] }}</p>
                                    <p class="text-xs text-gold-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                @if(!$notification->read_at)
                                <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs text-gold-400 hover:text-gold" title="Mark as read">📖</button>
                                </form>
                                @endif
                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-300" title="Delete">🗑️</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <p class="text-center text-ivory/50 py-8">✨ No notifications yet. Your cosmic journey awaits.</p>
            @endif
        </div>
    </div>
</div>

<script>
function notificationsManager() {
    return {
        init() {}
    }
}
</script>
@endsection
