<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image_path',
        'status',
        'is_published',
        'published_at',
        'is_featured',
        'meta_title',
        'meta_description',
        'category_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured'  => 'boolean',
        'published_at' => 'datetime',
    ];

    public static function booted()
    {
        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });

        static::created(function (Article $article) {
            try {
                $root = MediaCollection::findOrCreateRoot('Noticias', '#2563eb', 'newspaper');
                MediaCollection::firstOrCreate(
                    ['collectable_type' => static::class, 'collectable_id' => $article->id],
                    [
                        'name'      => $article->title,
                        'parent_id' => $root->id,
                        'color'     => '#2563eb',
                        'icon'      => 'newspaper',
                    ]
                );
            } catch (\Throwable) {
            }
        });

        static::deleting(function (Article $article) {
            MediaCollection::where('collectable_type', static::class)
                ->where('collectable_id', $article->id)
                ->delete();
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Media attached to the article via the article_media pivot.
     */
    public function media()
    {
        return $this->belongsToMany(Media::class, 'article_media')
            ->using(ArticleMedia::class)
            ->withPivot(['is_primary', 'position', 'caption', 'alt_text'])
            ->withTimestamps()
            ->orderBy('position');
    }

    /**
     * Revisions of this article.
     */
    public function revisions()
    {
        return $this->hasMany(ArticleRevision::class)->orderByDesc('revision_number');
    }

    /**
     * Scope: Filter by search term (title, excerpt, content)
     */
    public function scopeWhereSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        $searchTerm = '%' . $search . '%';
        return $query->where(function ($q) use ($searchTerm) {
            $q->where('title', 'like', $searchTerm)
                ->orWhere('excerpt', 'like', $searchTerm)
                ->orWhere('content', 'like', $searchTerm);
        });
    }

    /**
     * Scope: Filter by publication status (published, draft, all)
     */
    public function scopeWhereStatus($query, ?string $status)
    {
        if (!$status || $status === 'all') {
            return $query;
        }

        if ($status === 'published') {
            return $query->where('is_published', true);
        }

        if ($status === 'draft') {
            return $query->where('is_published', false);
        }

        return $query;
    }

    /**
     * Scope: Filter by date range (published_at)
     */
    public function scopeWhereDateRange($query, ?string $dateFrom, ?string $dateTo)
    {
        if ($dateFrom) {
            $query->whereDate('published_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('published_at', '<=', $dateTo);
        }

        return $query;
    }

    /**
     * Scope: Filter by category
     */
    public function scopeWhereCategory($query, ?int $categoryId)
    {
        if (!$categoryId) {
            return $query;
        }

        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope: Order by specified field
     */
    public function scopeOrderBySort($query, ?string $sort = 'published_at')
    {
        $validSorts = ['published_at', 'created_at', 'title'];

        if (!in_array($sort, $validSorts)) {
            $sort = 'published_at';
        }

        if ($sort === 'published_at') {
            return $query->orderByDesc('published_at');
        }

        if ($sort === 'created_at') {
            return $query->orderByDesc('created_at');
        }

        if ($sort === 'title') {
            return $query->orderBy('title', 'asc');
        }

        return $query->orderByDesc('published_at');
    }
}
