@extends('admin.layout')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Pending Prompts Review</h1>
    <a href="{{ route('admin.prompts.index') }}" class="px-4 py-2 bg-gray-600 text-white font-semibold hover:bg-gray-700 transition-colors">
        All Prompts
    </a>
</div>

<div class="bg-white shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted By</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($prompts as $prompt)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <img src="{{ $prompt->image_url }}" alt="{{ $prompt->title }}" class="w-16 h-16 object-cover">
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ $prompt->title }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ Str::limit($prompt->prompt_text, 60) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $prompt->submittedBy->name ?? 'Unknown' }}</div>
                    <div class="text-xs text-gray-500">{{ $prompt->submittedBy->email ?? '' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800">{{ $prompt->category->name ?? 'N/A' }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $prompt->created_at->format('M d, Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex gap-2">
                        <button onclick='showPromptModal(@json($prompt))' class="p-2 bg-blue-600 text-white hover:bg-blue-700 transition-colors" title="View">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                        <form action="{{ route('admin.prompts.approve', $prompt->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2 bg-green-600 text-white hover:bg-green-700 transition-colors" title="Approve">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        </form>
                        <button onclick="showRejectModal({{ $prompt->id }})" class="p-2 bg-red-600 text-white hover:bg-red-700 transition-colors" title="Reject">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    No pending prompts to review
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $prompts->links() }}
</div>

<!-- Prompt Details Modal -->
<div id="promptModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-xl font-bold" id="modalTitle"></h3>
                <button onclick="closePromptModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="modalContent"></div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white max-w-md w-full mx-4">
        <form id="rejectForm" method="POST">
            @csrf
            <div class="p-6">
                <h3 class="text-xl font-bold mb-4">Reject Prompt</h3>
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for rejection (optional)</label>
                <textarea name="reason" rows="4" class="w-full px-3 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Provide feedback to the user..."></textarea>
                <div class="flex gap-3 mt-4">
                    <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 font-semibold hover:bg-gray-300 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white font-semibold hover:bg-red-700 transition-colors">
                        Reject
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function showPromptModal(prompt) {
    document.getElementById('modalTitle').textContent = prompt.title;
    document.getElementById('modalContent').innerHTML = `
        <img src="${prompt.image_url}" alt="${prompt.title}" class="w-full h-64 object-cover mb-4">
        <div class="mb-4">
            <h4 class="font-semibold text-gray-700 mb-2">Prompt Text:</h4>
            <p class="text-gray-600 bg-gray-50 p-3">${prompt.prompt_text}</p>
        </div>
        ${prompt.how_to_use ? `
        <div class="mb-4">
            <h4 class="font-semibold text-gray-700 mb-2">How to Use:</h4>
            <p class="text-gray-600">${prompt.how_to_use}</p>
        </div>
        ` : ''}
    `;
    document.getElementById('promptModal').classList.remove('hidden');
    document.getElementById('promptModal').classList.add('flex');
}

function closePromptModal() {
    document.getElementById('promptModal').classList.add('hidden');
    document.getElementById('promptModal').classList.remove('flex');
}

function showRejectModal(id) {
    document.getElementById('rejectForm').action = `/admin/prompts/${id}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
}
</script>
@endsection
