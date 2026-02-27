<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Prompt, Blog, Category, Tag, Contact};
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'prompts' => Prompt::count(),
            'blogs' => Blog::count(),
            'categories' => Category::count(),
            'tags' => Tag::count(),
            'contacts' => Contact::count(),
            'unread_contacts' => Contact::where('is_read', false)->count(),
            'users' => \App\Models\User::count(),
            'google_users' => \App\Models\User::whereNotNull('google_id')->count(),
            'pending_prompts' => Prompt::where('status', 'pending')->count(),
            'total_views' => Prompt::sum('views'),
            'total_likes' => Prompt::sum('likes'),
        ];
        
        return view('admin.dashboard', compact('stats'));
    }

    public function prompts(Request $request)
    {
        $query = Prompt::with('category', 'submittedBy')->latest();
        
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }
        
        $prompts = $query->paginate(20);
        return view('admin.prompts.index', compact('prompts'));
    }

    public function pendingPrompts()
    {
        $prompts = Prompt::with('category', 'submittedBy')->where('status', 'pending')->latest()->paginate(20);
        return view('admin.prompts.pending', compact('prompts'));
    }

    public function approvePrompt($id)
    {
        $prompt = Prompt::findOrFail($id);
        $prompt->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now()
        ]);
        
        return redirect()->back()->with('success', 'Prompt approved successfully!');
    }

    public function rejectPrompt(Request $request, $id)
    {
        $prompt = Prompt::findOrFail($id);
        $prompt->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $request->input('reason')
        ]);
        
        return redirect()->back()->with('success', 'Prompt rejected.');
    }

    public function createPrompt()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.prompts.create', compact('categories', 'tags'));
    }

    public function storePrompt(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255|string',
            'author' => 'required|max:255|string',
            'prompt_text' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'how_to_use' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $validated['title'] = strip_tags($validated['title']);
        $validated['author'] = strip_tags($validated['author']);
        $validated['prompt_text'] = strip_tags($validated['prompt_text']);
        $validated['how_to_use'] = $validated['how_to_use'] ? strip_tags($validated['how_to_use']) : null;
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['status'] = 'approved';

        $path = $request->file('image')->store('prompts', 'public');
        $validated['image_url'] = '/storage/' . $path;

        $prompt = Prompt::create($validated);
        
        if ($request->has('tags')) {
            $prompt->tags()->sync($request->tags);
        }

        return redirect()->route('admin.prompts.index')->with('success', 'Prompt created successfully!');
    }

    public function editPrompt($id)
    {
        $prompt = Prompt::findOrFail($id);
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.prompts.edit', compact('prompt', 'categories', 'tags'));
    }

    public function updatePrompt(Request $request, $id)
    {
        $prompt = Prompt::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|max:255|string',
            'author' => 'required|max:255|string',
            'prompt_text' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'how_to_use' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $validated['title'] = strip_tags($validated['title']);
        $validated['author'] = strip_tags($validated['author']);
        $validated['prompt_text'] = strip_tags($validated['prompt_text']);
        $validated['how_to_use'] = $validated['how_to_use'] ? strip_tags($validated['how_to_use']) : null;
        $validated['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('prompts', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        $prompt->update($validated);
        
        if ($request->has('tags')) {
            $prompt->tags()->sync($request->tags);
        }

        return redirect()->route('admin.prompts.index')->with('success', 'Prompt updated successfully');
    }

    public function deletePrompt($id)
    {
        Prompt::findOrFail($id)->delete();
        return redirect()->route('admin.prompts.index')->with('success', 'Prompt deleted successfully');
    }

    public function blogs()
    {
        $blogs = Blog::latest()->paginate(20);
        return view('admin.blogs.index', compact('blogs'));
    }

    public function createBlog()
    {
        return view('admin.blogs.create');
    }

    public function storeBlog(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255|string',
            'author' => 'required|max:255|string',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $validated['title'] = strip_tags($validated['title']);
        $validated['author'] = strip_tags($validated['author']);
        $validated['excerpt'] = strip_tags($validated['excerpt']);
        $validated['is_published'] = $request->boolean('is_published');

        $path = $request->file('image')->store('blogs', 'public');
        $validated['image_url'] = '/storage/' . $path;

        Blog::create($validated);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog created successfully!');
    }

    public function editBlog($id)
    {
        $blog = Blog::findOrFail($id);
        return view('admin.blogs.edit', compact('blog'));
    }

    public function updateBlog(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|max:255|string',
            'author' => 'required|max:255|string',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $validated['title'] = strip_tags($validated['title']);
        $validated['author'] = strip_tags($validated['author']);
        $validated['excerpt'] = strip_tags($validated['excerpt']);
        $validated['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('blogs', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        $blog->update($validated);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog updated successfully!');
    }

    public function deleteBlog($id)
    {
        Blog::findOrFail($id)->delete();
        return redirect()->route('admin.blogs.index')->with('success', 'Blog deleted successfully!');
    }

    public function categories(Request $request)
    {
        $query = Category::withCount('prompts')->latest();
        
        if ($request->has('status') && $request->status) {
            $isActive = $request->status === 'active' ? 1 : 0;
            $query->where('is_active', $isActive);
        }
        
        $categories = $query->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function createCategory()
    {
        return view('admin.categories.create');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|string|unique:categories,name',
            'description' => 'nullable|max:500|string',
        ]);

        $validated['name'] = strip_tags($validated['name']);
        $validated['description'] = $validated['description'] ? strip_tags($validated['description']) : null;
        $validated['is_active'] = $request->boolean('is_active', true);

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|max:255|string|unique:categories,name,' . $id,
            'description' => 'nullable|max:500|string',
        ]);

        $validated['name'] = strip_tags($validated['name']);
        $validated['description'] = $validated['description'] ? strip_tags($validated['description']) : null;
        $validated['is_active'] = $request->boolean('is_active');

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        
        if ($category->prompts()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete category with existing prompts. Please reassign or delete prompts first.']);
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully!');
    }

    public function users()
    {
        $users = \App\Models\User::orderBy('name', 'asc')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function deleteUser($id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        if ($user->is_admin) {
            return back()->withErrors(['error' => 'Cannot delete admin user.']);
        }
        
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }

    public function contacts()
    {
        $contacts = Contact::latest()->paginate(20);
        return view('admin.contacts.index', compact('contacts'));
    }

    public function showContact($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->update(['is_read' => true]);
        return view('admin.contacts.show', compact('contact'));
    }

    public function deleteContact($id)
    {
        Contact::findOrFail($id)->delete();
        return redirect()->route('admin.contacts.index')->with('success', 'Contact deleted successfully!');
    }
}
