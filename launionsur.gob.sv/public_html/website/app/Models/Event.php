<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'event_date',
        'event_date_end',
        'location',
        'organizer',
        'featured_image_id',
        'status',
        'published_at',
        'author_id',
        'meta_title',
        'meta_description',
        'is_featured',
        'metadata',
        'category_id',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'event_date_end' => 'datetime',
        'published_at' => 'datetime',
        'metadata' => 'array',
        'is_featured' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getCategoryLabelAttribute(): string
    {
        if ($this->category_id && $this->relationLoaded('category') && $this->category) {
            return $this->category->name;
        }

        $raw = data_get($this->metadata, 'legacy_event_type')
            ?: data_get($this->metadata, 'category')
            ?: data_get($this->metadata, 'tipo')
            ?: 'Noticias';

        return Str::headline($raw);
    }

    /** Raw SQL expression that resolves the category from the metadata JSON (MySQL). */
    public static function categoryExpression(): string
    {
        return "LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.legacy_event_type')), JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.category')), JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.tipo'))))";
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->whereNotNull('published_at');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function featuredImage()
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withoutGlobalScope('article_type');
    }

    public function gallery()
    {
        return $this->hasMany(EventGallery::class)->orderBy('position');
    }
}
