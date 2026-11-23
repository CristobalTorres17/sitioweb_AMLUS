<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'svg_path_id',
        'description',
        'short_summary',
        'content',
        'hero_image_id',
        'map_image_id',
        'thumbnail_id',
        'color',
        'icon',
        'position',
        'is_active',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position'  => 'integer',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function heroImage()
    {
        return $this->belongsTo(Media::class, 'hero_image_id');
    }

    public function mapImage()
    {
        return $this->belongsTo(Media::class, 'map_image_id');
    }

    public function thumbnail()
    {
        return $this->belongsTo(Media::class, 'thumbnail_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /** Page key used in cms_block_page pivot and banners.page */
    public function pageKey(): string
    {
        return 'district:' . $this->slug;
    }

    /** CMS Blocks assigned to this district, grouped by zone */
    public function cmsBlocksByZone()
    {
        return CmsBlock::active()
            ->forPage($this->pageKey())
            ->get()
            ->groupBy('zone');
    }

    /** Banner slides assigned to this district */
    public function heroSlides()
    {
        return Banner::byPage($this->pageKey())
            ->with('media')
            ->get();
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
