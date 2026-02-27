@extends('admin.layout')

@section('title', 'Edit Category')

@section('content')
<h2 class="text-3xl font-bold mb-6">Edit Category</h2>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Category Name *</label>
            <input type="text" name="name" value="{{ $category->name }}" class="w-full border rounded px-3 py-2" required>
            <p class="text-sm text-gray-500 mt-1">Slug will be auto-generated from name</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Current Slug</label>
            <input type="text" value="{{ $category->slug }}" class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
            <p class="text-sm text-gray-500 mt-1">Auto-generated, will update when you change the name</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ $category->description }}</textarea>
            <p class="text-sm text-gray-500 mt-1">Optional: Brief description of this category</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Prompts in this category</label>
            <div class="bg-blue-50 border border-blue-200 rounded px-4 py-3">
                <span class="text-blue-800 font-semibold text-lg">{{ $category->prompts()->count() }} prompts</span>
            </div>
        </div>

        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }} class="mr-2">
                <span class="text-gray-700 font-bold">Active (Show on frontend)</span>
            </label>
        </div>

        <div class="flex space-x-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Update Category</button>
            <a href="{{ route('admin.categories.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">Cancel</a>
        </div>
    </form>
</div>
@endsection
