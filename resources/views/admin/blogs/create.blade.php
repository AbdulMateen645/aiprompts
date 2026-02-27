@extends('admin.layout')

@section('title', 'Create Blog')

@section('content')
<h2 class="text-3xl font-bold mb-6">Create New Blog</h2>

<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
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
            <label class="block text-gray-700 font-bold mb-2">Excerpt (Short Description)</label>
            <textarea name="excerpt" rows="3" class="w-full border rounded px-3 py-2" required>{{ old('excerpt') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Content</label>
            <div id="editor" style="min-height: 400px; border: 1px solid #ddd;"></div>
            <textarea name="content" id="content" style="display:none;" required>{{ old('content') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Image</label>
            <input type="file" name="image" class="w-full border rounded px-3 py-2" accept="image/*" required>
            <p class="text-sm text-gray-500 mt-1">Max size: 10MB</p>
        </div>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }} class="mr-2">
                <span class="text-gray-700 font-bold">Publish Blog</span>
            </label>
        </div>

        <div class="flex space-x-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Create Blog</button>
            <a href="{{ route('admin.blogs.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">Cancel</a>
        </div>
    </form>
</div>

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link'],
                    ['clean']
                ]
            }
        });
        
        // Update hidden textarea on content change
        quill.on('text-change', function() {
            document.querySelector('#content').value = quill.root.innerHTML;
        });
        
        // Also update on form submit
        document.querySelector('form').addEventListener('submit', function(e) {
            var content = quill.root.innerHTML;
            document.querySelector('#content').value = content;
            
            // Validate content is not empty
            if (content.trim() === '<p><br></p>' || content.trim() === '') {
                e.preventDefault();
                alert('Please enter blog content');
                return false;
            }
        });
    });
</script>
@endsection
