<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prompt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserPromptController extends Controller
{
    /**
     * Get user's prompt statistics
     */
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
    
    /**
     * Get user's submitted prompts
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $prompts = Prompt::where('submitted_by', $user->id)
            ->with('category')
            ->latest()
            ->paginate(10);
        
        // Ensure image URLs are absolute
        $prompts->getCollection()->transform(function ($prompt) {
            if ($prompt->image_url && !str_starts_with($prompt->image_url, 'http')) {
                $prompt->image_url = env('APP_URL', 'http://localhost:8000') . $prompt->image_url;
            }
            return $prompt;
        });
        
        return response()->json($prompts);
    }
    
    /**
     * Submit a new prompt
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255|string',
            'prompt_text' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'how_to_use' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);
        
        // Sanitize inputs
        $validated['title'] = strip_tags($validated['title']);
        $validated['prompt_text'] = strip_tags($validated['prompt_text']);
        $validated['how_to_use'] = $validated['how_to_use'] ? strip_tags($validated['how_to_use']) : null;
        
        // Handle image upload
        $path = $request->file('image')->store('prompts', 'public');
        $validated['image_url'] = '/storage/' . $path;
        
        // Set submission data
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
}
