<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class);
    }

    public function publishedPosts()
    {
        return $this->hasMany(BlogPost::class)->where('is_published', true);
    }
}

// ============================================

class BlogPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured',
        'read_time',
        'published_date',
        'views_count',
        'is_published',
        'category_id',
        'author_id',
        'featured_image',
        'tags',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'is_published' => 'boolean',
        'published_date' => 'datetime',
        'tags' => 'array',
    ];

    protected $dates = [
        'published_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            if (empty($post->published_date)) {
                $post->published_date = now();
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->where('published_date', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeByCategory($query, $categorySlug)
    {
        return $query->whereHas('category', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        });
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function getFormattedDateAttribute()
    {
        return $this->published_date->format('M d, Y');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}

// ============================================

class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'is_active',
        'verification_token',
        'verified_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscriber) {
            if (empty($subscriber->verification_token)) {
                $subscriber->verification_token = Str::random(64);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    public function isVerified()
    {
        return !is_null($this->verified_at);
    }

    public function verify()
    {
        $this->verified_at = now();
        $this->verification_token = null;
        $this->save();
    }
}
