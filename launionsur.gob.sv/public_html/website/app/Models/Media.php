<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
        'variants' => 'array',
        'usage_count' => 'integer',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_media')
            ->withPivot(['is_primary', 'position', 'caption', 'alt_text'])
            ->withTimestamps();
    }

    public function banners()
    {
        return $this->belongsToMany(Banner::class, 'banner_media')
            ->withPivot(['position', 'caption', 'alt_text'])
            ->withTimestamps();
    }

    public function cmsBlocks()
    {
        return $this->belongsToMany(CmsBlock::class, 'cms_block_media')
            ->withPivot(['position', 'caption', 'alt_text'])
            ->withTimestamps();
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_media')
            ->withPivot(['position', 'caption', 'alt_text'])
            ->withTimestamps();
    }
}
