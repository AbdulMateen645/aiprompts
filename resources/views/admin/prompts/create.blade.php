@extends('admin.layout')

@section('title', 'Create Prompt')

@section('content')
<h2 class="text-3xl font-bold mb-6">Create New Prompt</h2>

<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.prompts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Title</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Author</label>
            <input type="text" name="author" value="{{ old('author') }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Category</label>
            <select name="category_id" class="w-full border rounded px-3 py-2" required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Prompt Text</label>
            <textarea name="prompt_text" rows="5" class="w-full border rounded px-3 py-2" required>{{ old('prompt_text') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">How to Use (Optional)</label>
            <textarea name="how_to_use" rows="3" class="w-full border rounded px-3 py-2">{{ old('how_to_use') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">SEO Tags (5 required)</label>
            <input type="text" id="tagInput" class="w-full border rounded px-3 py-2 mb-2" placeholder="Type tag and press Enter (e.g., cyberpunk, portrait)" maxlength="50">
            <div id="tagsContainer" class="flex flex-wrap gap-2 mb-2"></div>
            <p class="text-sm text-gray-500"><span id="tagCount">0</span>/10 tags • Press Enter or comma to add • # symbol added automatically</p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Image</label>
            <input type="file" name="image" class="w-full border rounded px-3 py-2" accept="image/*" required>
            <p class="text-sm text-gray-500 mt-1">Max size: 10MB. Supported: JPG, PNG, GIF, WEBP, BMP</p>
        </div>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="mr-2">
                <span class="text-gray-700 font-bold">Featured Prompt</span>
            </label>
        </div>

        <div class="flex space-x-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Create Prompt</button>
            <a href="{{ route('admin.prompts.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">Cancel</a>
        </div>
    </form>
</div>

<script>
let tags = [];
const tagInput = document.getElementById('tagInput');
const tagsContainer = document.getElementById('tagsContainer');
const tagCount = document.getElementById('tagCount');

function addTag(tag) {
    tag = tag.trim().toLowerCase();
    if (tag && !tags.includes(tag) && tags.length < 10) {
        tags.push(tag);
        renderTags();
    }
    tagInput.value = '';
}

function removeTag(index) {
    tags.splice(index, 1);
    renderTags();
}

function renderTags() {
    tagsContainer.innerHTML = tags.map((tag, index) => `
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
            #${tag}
            <button type="button" onclick="removeTag(${index})" class="hover:text-blue-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </span>
    `).join('');
    tagCount.textContent = tags.length;
    tagCount.style.color = tags.length < 5 ? '#dc2626' : '#6b7280';
    
    // Update hidden inputs
    document.querySelectorAll('input[name^="tags["]').forEach(el => el.remove());
    tags.forEach((tag, index) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `tags[${index}]`;
        input.value = tag;
        tagsContainer.appendChild(input);
    });
}

tagInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        addTag(tagInput.value);
    }
});
</script>
@endsection
