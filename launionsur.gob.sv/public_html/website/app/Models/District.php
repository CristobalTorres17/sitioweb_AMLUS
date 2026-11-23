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
        'hero_image_id',
        'map_image_id',
        'thumbnail_id',
        'color',
        'icon',
        'position',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position'  => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

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
}
