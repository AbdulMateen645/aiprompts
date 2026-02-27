<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $slug = Str::slug($category->name);
                $count = 1;
                $originalSlug = $slug;
                
                while (static::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }
                
                $category->slug = $slug;
            }
        });
        
        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $slug = Str::slug($category->name);
                $count = 1;
                $originalSlug = $slug;
                
                while (static::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }
                
                $category->slug = $slug;
            }
        });
    }

    public function prompts()
    {
        return $this->hasMany(Prompt::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
