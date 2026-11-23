<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

// MediaCollection is in same namespace; no explicit import needed (App\Models)

class Event extends Model
{
    use HasFactory, SoftDeletes;

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

    protected static function booted()
    {
        static::creating(function (Event $event) {
            if (empty($event->slug) && !empty($event->title)) {
                $event->slug = Str::slug($event->title);

                $base = $event->slug;
                $index = 1;

                while (self::where('slug', $event->slug)->exists()) {
                    $event->slug = $base . '-' . $index++;
                }
            }
        });

        static::created(function (Event $event) {
            try {
                $root = MediaCollection::findOrCreateRoot('Eventos', '#16a34a', 'calendar');
                MediaCollection::firstOrCreate(
                    ['collectable_type' => static::class, 'collectable_id' => $event->id],
                    [
                        'name'      => $event->title,
                        'parent_id' => $root->id,
                        'color'     => '#16a34a',
                        'icon'      => 'calendar',
                        'created_by' => $event->author_id,
                    ]
                );
            } catch (\Throwable) {
            }
        });

        static::deleting(function (Event $event) {
            MediaCollection::where('collectable_type', static::class)
                ->where('collectable_id', $event->id)
                ->delete();
        });
    }

    /**
     * Scope: Filter by search term (title, description, content, location, organizer)
     */
    public function scopeWhereSearch($query, ?string $search)
    {
        if (! $search) {
            return $query;
        }

        $searchTerm = '%' . $search . '%';

        return $query->where(function ($builder) use ($searchTerm) {
            $builder->where('title', 'like', $searchTerm)
                ->orWhere('description', 'like', $searchTerm)
                ->orWhere('content', 'like', $searchTerm)
                ->orWhere('location', 'like', $searchTerm)
                ->orWhere('organizer', 'like', $searchTerm);
        });
    }

    /**
     * Scope: Filter by publication status (published, draft, archived, all)
     */
    public function scopeWhereStatus($query, ?string $status)
    {
        if (! $status || $status === 'all') {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * Scope: Filter by event date range.
     */
    public function scopeWhereDateRange($query, ?string $dateFrom, ?string $dateTo)
    {
        if ($dateFrom) {
            $query->whereDate('event_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('event_date', '<=', $dateTo);
        }

        return $query;
    }

    /**
     * Scope: Order by specified field.
     */
    public function scopeOrderBySort($query, ?string $sort = 'event_date')
    {
        $validSorts = ['event_date', 'created_at', 'title'];

        if (! in_array($sort, $validSorts, true)) {
            $sort = 'event_date';
        }

        if ($sort === 'event_date') {
            return $query->orderByDesc('event_date');
        }

        if ($sort === 'created_at') {
            return $query->orderByDesc('created_at');
        }

        if ($sort === 'title') {
            return $query->orderBy('title', 'asc');
        }

        return $query->orderByDesc('event_date');
    }

    /**
     * Category of the event
     */
    public function category()
    {
        return $this->belongsTo(Category::class)->withoutGlobalScope('article_type');
    }

    /**
     * Author of the event
     */
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Featured image
     */
    public function featuredImage()
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    /**
     * Gallery images for this event
     */
    public function gallery()
    {
        return $this->hasMany(EventGallery::class);
    }

    /**
     * Media in the gallery (through pivot)
     */
    public function galleryMedia()
    {
        return $this->belongsToMany(Media::class, 'event_galleries')
            ->withPivot('position', 'caption')
            ->withTimestamps()
            ->orderBy('position');
    }

    /**
     * Comments on this event
     */
    public function comments()
    {
        return $this->hasMany(EventComment::class);
    }

    /**
     * Approved comments only
     */
    public function approvedComments()
    {
        return $this->hasMany(EventComment::class)->where('status', 'approved');
    }

    /**
     * Scope for published events
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->whereNotNull('published_at');
    }

    /**
     * Scope for featured events
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for upcoming events
     */
    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now());
    }

    /**
     * Scope for past events
     */
    public function scopePast($query)
    {
        return $query->where('event_date', '<', now());
    }
}
