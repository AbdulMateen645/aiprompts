<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prompt;
use Illuminate\Http\Request;

class PromptController extends Controller
{
    public function index()
    {
        $prompts = Prompt::with(['category', 'tags'])
            ->approved()
            ->latest()
            ->get();
        return response()->json($prompts);
    }

    public function show($slug)
    {
        $prompt = Prompt::with(['category', 'tags'])
            ->where('slug', $slug)
            ->approved()
            ->firstOrFail();
        return response()->json($prompt);
    }

    public function incrementViews($id)
    {
        $prompt = Prompt::findOrFail($id);
        $prompt->incrementViews();
        return response()->json(['views' => $prompt->views]);
    }

    public function toggleLike(Request $request, $id)
    {
        $prompt = Prompt::findOrFail($id);
        
        $validated = $request->validate([
            'action' => 'required|in:like,unlike'
        ]);
        
        if ($validated['action'] === 'like') {
            $prompt->incrementLikes();
        } else {
            $prompt->decrementLikes();
        }
        
        return response()->json(['likes' => $prompt->likes]);
    }

    public function featured()
    {
        $prompts = Prompt::with(['category', 'tags'])
            ->where('is_featured', true)
            ->approved()
            ->latest()
            ->get();
        return response()->json($prompts);
    }

    public function byCategory($categorySlug)
    {
        $prompts = Prompt::with(['category', 'tags'])
            ->whereHas('category', function($query) use ($categorySlug) {
                $query->where('slug', $categorySlug);
            })
            ->approved()
            ->latest()
            ->get();
        return response()->json($prompts);
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'q' => 'required|string|max:255'
        ]);
        
        $query = $validated['q'];
        
        $prompts = Prompt::with(['category', 'tags'])
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('prompt_text', 'like', "%{$query}%")
                  ->orWhere('author', 'like', "%{$query}%");
            })
            ->approved()
            ->latest()
            ->get();
            
        return response()->json($prompts);
    }
}
