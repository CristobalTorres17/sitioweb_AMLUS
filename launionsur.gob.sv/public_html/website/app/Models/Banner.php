<?php

namespace App\Models;

use App\Support\MediaUrlResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $casts = [
        'show_button' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Append computed attributes when model is serialized.
     * This ensures `image_url` is available when the model is converted to arrays.
     *
     * @var array
     */
    protected $appends = ['image_url'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForPage(Builder $query, string $page): Builder
    {
        return $query->active()->where('page', $page)->orderBy('sort_order');
    }

    public function media()
    {
        return $this->belongsToMany(Media::class, 'banner_media')
            ->withPivot(['position', 'caption', 'alt_text'])
            ->withTimestamps()
            ->orderBy('position');
    }

    public function featuredMedia()
    {
        return $this->media()->first();
    }

    /**
     * Computed image URL for the banner.
     * Preference order:
     *  - first related media (url/path/file_path/storage_path/disk_path)
     *  - image_path column
     * Returns null when no candidate found.
     */
    public function getImageUrlAttribute(): ?string
    {
        $firstMedia = null;

        if ($this->relationLoaded('media')) {
            $firstMedia = $this->media->first();
        } else {
            $firstMedia = $this->media()->first();
        }

        $candidate = null;

        if ($firstMedia) {
            $candidate = data_get($firstMedia, 'url') ?:
                data_get($firstMedia, 'path') ?:
                data_get($firstMedia, 'file_path') ?:
                data_get($firstMedia, 'storage_path') ?:
                data_get($firstMedia, 'disk_path');
        }

        if (!$candidate && !empty($this->image_path)) {
            $candidate = $this->image_path;
        }

        if (!$candidate) {
            return null;
        }

        return MediaUrlResolver::resolve($candidate);
    }
}
