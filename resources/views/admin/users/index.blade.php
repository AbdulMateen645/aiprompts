@extends('admin.layout')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Registered Users</h1>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($users as $user)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">{{ $loop->iteration }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-3">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        @endif
                        <div class="w-8 h-8 rounded-full bg-blue-600 items-center justify-center text-white font-semibold text-xs {{ $user->avatar ? 'hidden' : 'flex' }}">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <span class="font-medium">{{ $user->name }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $user->email }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($user->is_admin)
                        <span class="px-2 py-1 text-xs rounded bg-purple-100 text-purple-800 font-semibold">Admin</span>
                    @elseif($user->google_id)
                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800 font-semibold">Google</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800 font-semibold">Manual</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $user->created_at->format('M d, Y') }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if(!$user->is_admin)
                        <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="inline" id="delete-form-{{ $user->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="showDeleteModal(document.getElementById('delete-form-{{ $user->id }}'), 'Are you sure you want to delete user {{ $user->name }}? This action cannot be undone.')" class="text-red-600 hover:text-red-800 transition-colors" title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    @else
                        <span class="text-gray-400 text-xs">Protected</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No users yet</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection
