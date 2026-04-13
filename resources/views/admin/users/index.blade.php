@extends('admin.layouts.app')

@section('content')
<div class="admin-card p-5">
    <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
        <h1 class="text-2xl font-bold text-gold">👥 User Management</h1>
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name/email" class="input-golden text-sm py-1 px-3">
            <button type="submit" class="btn-golden text-sm py-1 px-3">Search</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-gold/30">
                <tr class="text-gold-400">
                    <th class="px-4 py-2">ID</th><th>Name</th><th>Email</th><th>Admin</th><th>Verified</th><th>KYC</th><th>Joined</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="border-b border-gold/10 hover:bg-gold/5">
                    <td class="px-4 py-2">{{ $user->id }}</td>
                    <td class="px-4 py-2">{{ $user->name }}</td>
                    <td class="px-4 py-2">{{ $user->email }}</td>
                    <td class="px-4 py-2">@if($user->is_admin) ✅ @else ❌ @endif</td>
                    <td class="px-4 py-2">@if($user->is_verified) ✅ @else ❌ @endif</td>
                    <td class="px-4 py-2">{{ ucfirst($user->kyc_status ?? 'pending') }}</td>
                    <td class="px-4 py-2">{{ $user->created_at->format('Y-m-d') }}</td>
                    <td class="px-4 py-2 flex gap-2">
                        <a href="{{ route('admin.users.show', $user) }}" class="text-gold-400 hover:text-gold">View</a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-400">Edit</a>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete user?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400">Del</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
</div>
@endsection
