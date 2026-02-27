<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::active()
            ->withCount('prompts')
            ->orderBy('name')
            ->get();
        return response()->json($categories);
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->with(['prompts' => function($query) {
                $query->approved()->latest();
            }])
            ->firstOrFail();
        return response()->json($category);
    }
}
