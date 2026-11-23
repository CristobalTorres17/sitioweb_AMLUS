<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use App\Models\Event;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'filename',
        'original_filename',
        'path',
        'variants',
        'mime_type',
        'size',
        'width',
        'height',
        'disk',
        'user_id',
        'collection_id',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'json',
        'variants' => 'json',
        'usage_count' => 'integer',
    ];

    /**
     * The folder (MediaCollection) this media belongs to.
     */
    public function collection()
    {
        return $this->belongsTo(MediaCollection::class, 'collection_id');
    }

    /**
     * User who uploaded this media
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Articles this media is attached to via article_media pivot.
     */
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_media')
            ->using(ArticleMedia::class)
            ->withPivot(['is_primary', 'position', 'caption', 'alt_text'])
            ->withTimestamps();
    }

    /**
     * Tags for categorizing this media
     */
    public function tags()
    {
        return $this->belongsToMany(MediaTag::class, 'media_tag', 'media_id', 'media_tags_id')
            ->withTimestamps();
    }

    /**
     * Banners this media is attached to via banner_media pivot.
     */
    public function banners()
    {
        return $this->belongsToMany(Banner::class, 'banner_media')
            ->withPivot(['position', 'caption', 'alt_text'])
            ->withTimestamps()
            ->orderBy('position');
    }

    public function cmsBlocks()
    {
        return $this->belongsToMany(CmsBlock::class, 'cms_block_media')
            ->withPivot(['position', 'caption', 'alt_text'])
            ->withTimestamps()
            ->orderBy('position');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_media')
            ->withPivot(['position', 'caption', 'alt_text'])
            ->withTimestamps()
            ->orderBy('position');
    }

    /**
     * Events this media appears in as a gallery image (event_galleries pivot).
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_galleries')
            ->withPivot('position', 'caption')
            ->withTimestamps();
    }

    /**
     * Events that use this media as their featured image.
     */
    public function featuredInEvents()
    {
        return $this->hasMany(Event::class, 'featured_image_id');
    }

    /**
     * Compute where this media is used across the site.
     * Returns arrays of items (id, slug/title) for key modules and counts.
     */
    public function usedIn(): array
    {
        $result = [];

        // Articles: prefer `title`, fallback to `name` or `slug`
        $artsQuery = $this->articles()->select('articles.id');
        if (Schema::hasColumn('articles', 'title')) {
            $artsQuery->addSelect('articles.title');
        } elseif (Schema::hasColumn('articles', 'name')) {
            $artsQuery->addSelect('articles.name as title');
        } elseif (Schema::hasColumn('articles', 'slug')) {
            $artsQuery->addSelect('articles.slug as title');
        }
        $articles = $artsQuery->get()->toArray();
        $result['articles'] = $articles;

        // Banners: prefer `title`
        $bannersQuery = $this->banners()->select('banners.id');
        if (Schema::hasColumn('banners', 'title')) {
            $bannersQuery->addSelect('banners.title');
        } elseif (Schema::hasColumn('banners', 'name')) {
            $bannersQuery->addSelect('banners.name as title');
        }
        $banners = $bannersQuery->get()->toArray();
        $result['banners'] = $banners;

        // CMS blocks: prefer `name` then `title`
        $blocksQuery = $this->cmsBlocks()->select('cms_blocks.id');
        if (Schema::hasColumn('cms_blocks', 'name')) {
            $blocksQuery->addSelect('cms_blocks.name');
        } elseif (Schema::hasColumn('cms_blocks', 'title')) {
            $blocksQuery->addSelect('cms_blocks.title as name');
        }
        $blocks = $blocksQuery->get()->toArray();
        $result['cms_blocks'] = $blocks;

        // Services: some legacy DBs use `title` or `slug` instead of `name`.
        $servicesQuery = $this->services()->select('services.id');
        if (Schema::hasColumn('services', 'name')) {
            $servicesQuery->addSelect('services.name');
        } elseif (Schema::hasColumn('services', 'title')) {
            $servicesQuery->addSelect('services.title as name');
        } elseif (Schema::hasColumn('services', 'slug')) {
            $servicesQuery->addSelect('services.slug as name');
        }
        $services = $servicesQuery->get()->toArray();
        $result['services'] = $services;

        $events = $this->events()->select('events.id', 'events.title')->get()->toArray();
        $featuredEvents = $this->featuredInEvents()->select('id', 'title')->get()->toArray();
        $result['events'] = $events;
        $result['featured_events'] = $featuredEvents;

        $result['total_count'] = count($articles) + count($banners) + count($blocks) + count($services)
            + count($events) + count($featuredEvents);

        return $result;
    }
}
