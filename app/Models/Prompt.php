<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Prompt extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'image_url',
        'author',
        'prompt_text',
        'views',
        'likes',
        'category_id',
        'how_to_use',
        'is_featured',
        'pricing_type',
        'price',
        'is_premium',
        'access_level',
        'status',
        'submitted_by',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_premium' => 'boolean',
        'price' => 'decimal:2',
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
        
        static::creating(function ($prompt) {
            if (empty($prompt->slug)) {
                $slug = Str::slug($prompt->title);
                $count = 1;
                $originalSlug = $slug;
                
                while (static::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }
                
                $prompt->slug = $slug;
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function incrementLikes()
    {
        $this->increment('likes');
    }

    public function decrementLikes()
    {
        $this->decrement('likes');
    }

    public function isFree()
    {
        return $this->pricing_type === 'free';
    }

    public function isPaid()
    {
        return $this->pricing_type === 'paid';
    }

    public function isPremium()
    {
        return $this->pricing_type === 'premium' || $this->is_premium;
    }

    public function scopeFree($query)
    {
        return $query->where('pricing_type', 'free');
    }

    public function scopePaid($query)
    {
        return $query->where('pricing_type', 'paid');
    }

    public function scopePremium($query)
    {
        return $query->where('pricing_type', 'premium')->orWhere('is_premium', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
