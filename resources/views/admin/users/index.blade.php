@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Users</h2>
</div>

<form method="GET" class="mb-4">
    <input type="text" name="search" placeholder="Search by name, email, phone" value="{{ request('search') }}" class="px-4 py-2 border rounded w-full md:w-1/3">
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded ml-2">Search</button>
</form>

<table class="min-w-full bg-white rounded-lg overflow-hidden">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-6 py-3 text-left">ID</th>
            <th class="px-6 py-3 text-left">Name</th>
            <th class="px-6 py-3 text-left">Email</th>
            <th class="px-6 py-3 text-left">Phone</th>
            <th class="px-6 py-3 text-left">Verified</th>
            <th class="px-6 py-3 text-left">Admin</th>
            <th class="px-6 py-3 text-left">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr class="border-t">
            <td class="px-6 py-4">{{ $user->id }}</td>
            <td class="px-6 py-4">{{ $user->name }}</td>
            <td class="px-6 py-4">{{ $user->email }}</td>
            <td class="px-6 py-4">{{ $user->phone }}</td>
            <td class="px-6 py-4">{{ $user->is_verified ? 'Yes' : 'No' }}</td>
            <td class="px-6 py-4">{{ $user->is_admin ? 'Yes' : 'No' }}</td>
            <td class="px-6 py-4">
                <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:underline mr-2">View</a>
                <a href="{{ route('admin.users.edit', $user) }}" class="text-green-600 hover:underline mr-2">Edit</a>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Delete user?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection
