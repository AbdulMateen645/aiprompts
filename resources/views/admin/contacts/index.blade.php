@extends('admin.layout')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Contact Messages</h1>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($contacts as $contact)
            <tr class="{{ !$contact->is_read ? 'bg-blue-50' : '' }}">
                <td class="px-6 py-4 whitespace-nowrap">{{ $contact->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $contact->email }}</td>
                <td class="px-6 py-4">{{ $contact->subject ?? 'No subject' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $contact->created_at->format('M d, Y') }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($contact->is_read)
                        <span class="px-2 py-1 text-xs rounded bg-gray-200">Read</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded bg-blue-500 text-white">Unread</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex gap-3">
                        <a href="{{ route('admin.contacts.show', $contact->id) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="View">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                        <form action="{{ route('admin.contacts.delete', $contact->id) }}" method="POST" class="inline" id="delete-contact-{{ $contact->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="showDeleteModal(document.getElementById('delete-contact-{{ $contact->id }}'), 'Are you sure you want to delete this contact message?')" class="text-red-600 hover:text-red-800 transition-colors" title="Delete">
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
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No contact messages yet</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $contacts->links() }}
</div>
@endsection
