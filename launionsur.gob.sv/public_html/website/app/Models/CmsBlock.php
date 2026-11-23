<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CmsBlock extends Model
{
    protected $table = 'cms_blocks';

    protected $fillable = [
        'name',
        'slug',
        'template_key',
        'zone',
        'template_data',
        'content',
        'excerpt',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'template_data' => 'array',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForPage(Builder $query, string $page): Builder
    {
        return $query->join('cms_block_page', 'cms_block_page.cms_block_id', '=', 'cms_blocks.id')
            ->where('cms_block_page.page', $page)
            ->orderBy('cms_block_page.position')
            ->select('cms_blocks.*');
    }

    public function scopeForZone(Builder $query, string $zone): Builder
    {
        return $query->where('zone', $zone);
    }

    public function scopeForPageZone(Builder $query, string $page, ?string $zone = null): Builder
    {
        return $query->join('cms_block_page', 'cms_block_page.cms_block_id', '=', 'cms_blocks.id')
            ->where('cms_block_page.page', $page)
            ->when($zone, fn(Builder $builder) => $builder->where('cms_blocks.zone', $zone))
            ->orderBy('cms_block_page.position')
            ->select('cms_blocks.*');
    }

    public function scopeBySlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    public function media()
    {
        return $this->belongsToMany(Media::class, 'cms_block_media')
            ->withPivot(['position', 'caption', 'alt_text'])
            ->withTimestamps();
    }
}
