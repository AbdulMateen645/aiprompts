<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prompt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserPromptController extends Controller
{
    public function stats(Request $request)
    {
        $user = $request->user();
        
        $stats = [
            'total' => Prompt::where('submitted_by', $user->id)->count(),
            'approved' => Prompt::where('submitted_by', $user->id)->where('status', 'approved')->count(),
            'pending' => Prompt::where('submitted_by', $user->id)->where('status', 'pending')->count(),
            'rejected' => Prompt::where('submitted_by', $user->id)->where('status', 'rejected')->count(),
        ];
        
        return response()->json($stats);
    }
    
    public function index(Request $request)
    {
        $user = $request->user();
        
        $prompts = Prompt::where('submitted_by', $user->id)
            ->with('category')
            ->latest()
            ->paginate(10);
        
        $prompts->getCollection()->transform(function ($prompt) {
            if ($prompt->image_url && !str_starts_with($prompt->image_url, 'http')) {
                $prompt->image_url = env('APP_URL', 'http://localhost:8000') . $prompt->image_url;
            }
            return $prompt;
        });
        
        return response()->json($prompts);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255|string',
            'prompt_text' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'how_to_use' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'tags' => 'required|array|min:5|max:10',
            'tags.*' => 'string|max:50',
        ]);
        
        $validated['title'] = strip_tags($validated['title']);
        $validated['prompt_text'] = strip_tags($validated['prompt_text']);
        $validated['how_to_use'] = $validated['how_to_use'] ? strip_tags($validated['how_to_use']) : null;
        
        if (isset($validated['tags'])) {
            $validated['tags'] = array_map('strtolower', array_map('trim', $validated['tags']));
            $validated['tags'] = array_unique(array_filter($validated['tags']));
        }
        
        $path = $request->file('image')->store('prompts', 'public');
        $validated['image_url'] = '/storage/' . $path;
        
        $validated['author'] = $request->user()->name;
        $validated['status'] = 'pending';
        $validated['submitted_by'] = $request->user()->id;
        $validated['is_featured'] = false;
        
        $prompt = Prompt::create($validated);
        
        return response()->json([
            'message' => 'Prompt submitted successfully! It will be reviewed by our team.',
            'prompt' => $prompt
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $prompt = Prompt::where('id', $id)
            ->where('submitted_by', $request->user()->id)
            ->with('category')
            ->firstOrFail();
        
        return response()->json($prompt);
    }

    public function update(Request $request, $id)
    {
        $prompt = Prompt::where('id', $id)
            ->where('submitted_by', $request->user()->id)
            ->firstOrFail();

        if ($prompt->status === 'approved') {
            return response()->json([
                'message' => 'Cannot edit approved prompts. Please contact admin.'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'required|max:255|string',
            'prompt_text' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'how_to_use' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'tags' => 'required|array|min:5|max:10',
            'tags.*' => 'string|max:50',
        ]);

        $validated['title'] = strip_tags($validated['title']);
        $validated['prompt_text'] = strip_tags($validated['prompt_text']);
        $validated['how_to_use'] = $validated['how_to_use'] ? strip_tags($validated['how_to_use']) : null;

        if (isset($validated['tags'])) {
            $validated['tags'] = array_map('strtolower', array_map('trim', $validated['tags']));
            $validated['tags'] = array_unique(array_filter($validated['tags']));
        }

        if ($request->hasFile('image')) {
            if ($prompt->image_url && !str_starts_with($prompt->image_url, 'http')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $prompt->image_url));
            }
            $path = $request->file('image')->store('prompts', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        if ($prompt->status === 'rejected') {
            $validated['status'] = 'pending';
            $validated['rejection_reason'] = null;
        }

        $prompt->update($validated);

        return response()->json([
            'message' => 'Prompt updated successfully!',
            'prompt' => $prompt->fresh('category')
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $prompt = Prompt::where('id', $id)
            ->where('submitted_by', $request->user()->id)
            ->firstOrFail();

        if ($prompt->status === 'approved') {
            return response()->json([
                'message' => 'Cannot delete approved prompts. Please contact admin.'
            ], 403);
        }

        if ($prompt->image_url && !str_starts_with($prompt->image_url, 'http')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $prompt->image_url));
        }

        $prompt->delete();

        return response()->json([
            'message' => 'Prompt deleted successfully!'
        ]);
    }
}
