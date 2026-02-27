@extends('admin.layout')

@section('title', 'Manage Categories')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Manage Categories</h2>
    <div class="flex gap-3">
        <div class="relative">
            <select onchange="window.location.href=this.value" class="appearance-none pl-10 pr-10 py-2 bg-white border-2 border-gray-200 rounded-lg text-gray-700 focus:outline-none focus:border-blue-500 transition-all text-sm font-medium cursor-pointer hover:border-blue-400">
                <option value="{{ route('admin.categories.index') }}">All Status</option>
                <option value="{{ route('admin.categories.index', ['status' => 'active']) }}" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="{{ route('admin.categories.index', ['status' => 'inactive']) }}" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <svg class="w-4 h-4 text-gray-600 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <svg class="w-4 h-4 text-gray-600 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add New Category</a>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prompts</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($categories as $category)
            <tr>
                <td class="px-6 py-4 font-medium">{{ $category->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $category->slug }}</td>
                <td class="px-6 py-4 text-sm">{{ Str::limit($category->description, 50) }}</td>
                <td class="px-6 py-4">
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">{{ $category->prompts_count }}</span>
                </td>
                <td class="px-6 py-4">
                    @if($category->is_active)
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Active</span>
                    @else
                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">Inactive</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:bg-blue-50 rounded transition" title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <form action="{{ route('admin.categories.delete', $category->id) }}" method="POST" class="inline" onsubmit="event.preventDefault(); showDeleteModal(this, 'Are you sure you want to delete this category? This will fail if category has prompts.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:bg-red-50 rounded transition" title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No categories found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $categories->links() }}
</div>
@endsection
