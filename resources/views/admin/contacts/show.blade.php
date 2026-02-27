@extends('admin.layout')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.contacts.index') }}" class="text-blue-600 hover:text-blue-900">&larr; Back to Contacts</a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold mb-6">Contact Message</h1>
    
    <div class="space-y-4">
        <div>
            <label class="text-sm font-medium text-gray-500">Name</label>
            <p class="text-lg">{{ $contact->name }}</p>
        </div>
        
        <div>
            <label class="text-sm font-medium text-gray-500">Email</label>
            <p class="text-lg"><a href="mailto:{{ $contact->email }}" class="text-blue-600">{{ $contact->email }}</a></p>
        </div>
        
        @if($contact->subject)
        <div>
            <label class="text-sm font-medium text-gray-500">Subject</label>
            <p class="text-lg">{{ $contact->subject }}</p>
        </div>
        @endif
        
        <div>
            <label class="text-sm font-medium text-gray-500">Message</label>
            <p class="text-lg whitespace-pre-wrap">{{ $contact->message }}</p>
        </div>
        
        <div>
            <label class="text-sm font-medium text-gray-500">Received</label>
            <p class="text-lg">{{ $contact->created_at->format('F d, Y \a\t h:i A') }}</p>
        </div>
    </div>
    
    <div class="mt-6 flex gap-3">
        <a href="mailto:{{ $contact->email }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Reply via Email</a>
        <form action="{{ route('admin.contacts.delete', $contact->id) }}" method="POST" class="inline" id="delete-contact-form">
            @csrf
            @method('DELETE')
            <button type="button" onclick="showDeleteModal(document.getElementById('delete-contact-form'), 'Are you sure you want to delete this contact message?')" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
        </form>
    </div>
</div>
@endsection
