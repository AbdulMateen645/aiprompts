@extends('admin.layout')

@section('title', 'Edit Prompt')

@section('content')
<h2 class="text-3xl font-bold mb-6">Edit Prompt</h2>

<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.prompts.update', $prompt->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Title</label>
            <input type="text" name="title" value="{{ $prompt->title }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Author</label>
            <input type="text" name="author" value="{{ $prompt->author }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Category</label>
            <select name="category_id" class="w-full border rounded px-3 py-2" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $prompt->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Prompt Text</label>
            <textarea name="prompt_text" rows="5" class="w-full border rounded px-3 py-2" required>{{ $prompt->prompt_text }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">How to Use</label>
            <textarea name="how_to_use" rows="3" class="w-full border rounded px-3 py-2">{{ $prompt->how_to_use }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Current Image</label>
            <img src="{{ $prompt->image_url }}" alt="{{ $prompt->title }}" class="w-32 h-32 object-cover mb-2">
            <input type="file" name="image" class="w-full border rounded px-3 py-2" accept="image/*">
            <p class="text-sm text-gray-500">Leave empty to keep current image</p>
        </div>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="is_featured" value="1" {{ $prompt->is_featured ? 'checked' : '' }} class="mr-2">
                <span class="text-gray-700 font-bold">Featured Prompt</span>
            </label>
        </div>

        <div class="flex space-x-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Update Prompt</button>
            <a href="{{ route('admin.prompts.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">Cancel</a>
        </div>
    </form>
</div>
@endsection