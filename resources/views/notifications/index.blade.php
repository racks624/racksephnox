@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gold shimmer-gold">🔔 Notifications</h1>
                <button onclick="markAllRead()" class="btn-outline-silver text-sm py-1 px-3">Mark all read</button>
            </div>
            <div class="space-y-3">
                @forelse($notifications as $notification)
                <div class="admin-card p-4 flex justify-between items-center {{ $notification->read_at ? 'opacity-70' : 'border-l-4 border-gold' }}">
                    <div class="flex-1">
                        <p class="text-ivory">{{ $notification->data['message'] ?? 'New notification' }}</p>
                        <p class="text-xs text-gold-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$notification->read_at)
                    <button onclick="markRead({{ $notification->id }})" class="text-gold-400 hover:text-gold text-xs ml-2">Mark read</button>
                    @endif
                </div>
                @empty
                <p class="text-center text-ivory/50 py-8">✨ No notifications yet. Check back later.</p>
                @endforelse
            </div>
            {{ $notifications->links() }}
        </div>
    </div>
</div>
<script>
    function markRead(id) { fetch('/api/notifications/'+id+'/read', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>location.reload()); }
    function markAllRead() { fetch('/api/notifications/mark-all-read', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>location.reload()); }
</script>
@endsection
