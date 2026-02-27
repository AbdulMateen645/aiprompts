<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'image_url',
        'excerpt',
        'content',
        'author',
        'category_id',
        'views',
        'is_published'
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    protected $appends = ['full_image_url'];

    public function getFullImageUrlAttribute()
    {
        if (!$this->image_url) return null;
        
        if (str_starts_with($this->image_url, 'http')) {
            return $this->image_url;
        }
        
        return env('APP_URL', 'http://localhost:8000') . $this->image_url;
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $slug = Str::slug($blog->title);
                $count = 1;
                $originalSlug = $slug;
                
                while (static::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }
                
                $blog->slug = $slug;
            }
        });
        
        static::updating(function ($blog) {
            if ($blog->isDirty('title') && empty($blog->slug)) {
                $slug = Str::slug($blog->title);
                $count = 1;
                $originalSlug = $slug;
                
                while (static::where('slug', $slug)->where('id', '!=', $blog->id)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }
                
                $blog->slug = $slug;
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
