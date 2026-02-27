<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Prompt;
use App\Models\Tag;
use App\Models\Blog;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create Categories
        $categories = [
            ['name' => 'AI Art', 'slug' => 'ai-art', 'description' => 'AI-generated art prompts'],
            ['name' => 'Writing', 'slug' => 'writing', 'description' => 'Creative writing prompts'],
            ['name' => 'Coding', 'slug' => 'coding', 'description' => 'Programming assistance prompts'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Tags
        $tags = [
            ['name' => 'Popular', 'slug' => 'popular'],
            ['name' => 'Beginner', 'slug' => 'beginner'],
            ['name' => 'Advanced', 'slug' => 'advanced'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }

        // Create Prompts
        $prompts = [
            [
                'title' => 'Create Stunning AI Art',
                'slug' => 'create-stunning-ai-art',
                'image_url' => '/storage/prompts/default.jpg',
                'author' => 'John Doe',
                'prompt_text' => 'Generate a beautiful landscape with mountains and sunset',
                'category_id' => 1,
                'how_to_use' => 'Use this prompt with any AI art generator',
                'is_featured' => true,
                'views' => 150,
                'likes' => 45,
            ],
            [
                'title' => 'Write a Short Story',
                'slug' => 'write-short-story',
                'image_url' => '/storage/prompts/default.jpg',
                'author' => 'Jane Smith',
                'prompt_text' => 'Write a compelling short story about time travel',
                'category_id' => 2,
                'how_to_use' => 'Use this as a starting point for creative writing',
                'is_featured' => false,
                'views' => 89,
                'likes' => 23,
            ],
        ];

        foreach ($prompts as $promptData) {
            $prompt = Prompt::create($promptData);
            $prompt->tags()->attach([1, 2]);
        }

        // Create Blogs
        $blogs = [
            [
                'title' => 'Getting Started with AI Prompts',
                'slug' => 'getting-started-ai-prompts',
                'image_url' => '/storage/blogs/default.jpg',
                'author' => 'Admin',
                'excerpt' => 'Learn the basics of crafting effective AI prompts',
                'content' => 'This is a comprehensive guide to AI prompts...',
                'category_id' => 1,
                'is_published' => true,
                'views' => 200,
            ],
        ];

        foreach ($blogs as $blog) {
            Blog::create($blog);
        }
    }
}
