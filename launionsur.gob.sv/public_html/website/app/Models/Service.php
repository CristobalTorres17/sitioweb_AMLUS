<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'order' => 'integer',
        'featured_order' => 'integer',
        'documentation' => 'array',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)->orderBy('featured_order');
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function steps()
    {
        return $this->hasMany(ServiceStep::class)->orderBy('sort_order');
    }

    public function requirements()
    {
        return $this->hasMany(ServiceRequirement::class)->orderBy('sort_order');
    }

    public function media()
    {
        return $this->belongsToMany(Media::class, 'service_media')
            ->withPivot(['position', 'caption', 'alt_text'])
            ->withTimestamps()
            ->orderBy('position');
    }
}
