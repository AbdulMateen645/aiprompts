@extends('admin.layout')

@section('title', 'Manage Prompts')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold">Manage Prompts</h2>
    <div class="flex gap-3">
        <div class="relative">
            <select onchange="window.location.href=this.value" class="appearance-none pl-10 pr-10 py-2 bg-white border-2 border-gray-200 rounded-lg text-gray-700 focus:outline-none focus:border-blue-500 transition-all text-sm font-medium cursor-pointer hover:border-blue-400">
                <option value="{{ route('admin.prompts.index') }}">All Categories</option>
                @foreach(\App\Models\Category::all() as $cat)
                    <option value="{{ route('admin.prompts.index', ['category' => $cat->id]) }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <svg class="w-4 h-4 text-gray-600 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <svg class="w-4 h-4 text-gray-600 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
        <a href="{{ route('admin.prompts.pending') }}" class="bg-orange-600 text-white px-4 py-2 font-semibold hover:bg-orange-700 transition-colors">
            Pending Review
            @php
                $pendingCount = \App\Models\Prompt::where('status', 'pending')->count();
            @endphp
            @if($pendingCount > 0)
                <span class="ml-2 bg-white text-orange-600 px-2 py-1 text-xs font-bold">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.prompts.create') }}" class="bg-blue-600 text-white px-4 py-2 font-semibold hover:bg-blue-700 transition-colors">Add New Prompt</a>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Picture</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Views</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Likes</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($prompts as $prompt)
            <tr>
                <td class="px-6 py-4">
                    <img src="{{ $prompt->image_url }}" alt="{{ $prompt->title }}" class="w-16 h-16 object-cover rounded">
                </td>
                <td class="px-6 py-4">{{ $prompt->title }}</td>
                <td class="px-6 py-4">{{ $prompt->category->name }}</td>
                <td class="px-6 py-4">{{ $prompt->author }}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-1">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span class="font-semibold">{{ number_format($prompt->views) }}</span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-1">
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                        <span class="font-semibold">{{ number_format($prompt->likes) }}</span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        @if($prompt->is_pinned)
                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs font-semibold flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L11 4.323V3a1 1 0 011-1zm-5 8.274l-.818 2.552c-.25.78.409 1.674 1.318 1.674H8.5v5.5a1 1 0 102 0V14.5h3c.909 0 1.568-.894 1.318-1.674l-.818-2.552a1 1 0 00-.95-.694H5.95a1 1 0 00-.95.694z"/>
                                </svg>
                                Pinned
                            </span>
                        @endif
                        @if($prompt->is_featured)
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">Featured</span>
                        @endif
                        @if(!$prompt->is_pinned && !$prompt->is_featured)
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">Normal</span>
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-2">
                        <form action="{{ route('admin.prompts.togglePin', $prompt->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 {{ $prompt->is_pinned ? 'text-purple-600 bg-purple-50' : 'text-gray-400 hover:bg-gray-50' }} rounded transition" title="{{ $prompt->is_pinned ? 'Unpin' : 'Pin to Top' }}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L11 4.323V3a1 1 0 011-1zm-5 8.274l-.818 2.552c-.25.78.409 1.674 1.318 1.674H8.5v5.5a1 1 0 102 0V14.5h3c.909 0 1.568-.894 1.318-1.674l-.818-2.552a1 1 0 00-.95-.694H5.95a1 1 0 00-.95.694z"/>
                                </svg>
                            </button>
                        </form>
                        <a href="{{ route('admin.prompts.edit', $prompt->id) }}" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:bg-blue-50 rounded transition" title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <form action="{{ route('admin.prompts.delete', $prompt->id) }}" method="POST" class="inline" onsubmit="event.preventDefault(); showDeleteModal(this, 'Are you sure you want to delete this prompt? This action cannot be undone.');">
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
                <td colspan="8" class="px-6 py-4 text-center text-gray-500">No prompts found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $prompts->links() }}
</div>
@endsection
