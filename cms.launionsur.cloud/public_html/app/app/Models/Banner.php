<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Banner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'cta_text',
        'cta_url',
        'show_button',
        'page',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'show_button' => 'boolean',
        'is_active' => 'boolean',
    ];

    public static function booted()
    {
        static::creating(function ($banner) {
            if (empty($banner->sort_order)) {
                $banner->sort_order = self::where('page', $banner->page)->count();
            }
        });
    }

    /**
     * Media attached to the banner via the banner_media pivot.
     */
    public function media()
    {
        return $this->belongsToMany(Media::class, 'banner_media')
            ->withPivot(['position', 'caption', 'alt_text'])
            ->withTimestamps()
            ->orderBy('position');
    }

    /**
     * Get the primary media (featured image) for the banner.
     */
    public function featuredMedia()
    {
        return $this->media()->first();
    }

    /**
     * Scope: Filter by page
     */
    public function scopeByPage($query, string $page)
    {
        return $query->where('page', $page)->where('is_active', true)->orderBy('sort_order');
    }
}
