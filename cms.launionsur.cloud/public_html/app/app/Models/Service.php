<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    public const KIND_SERVICE = 'service';

    public const KIND_PROCEDURE = 'procedure';

    protected $appends = [
        'kind_label',
    ];

    protected $fillable = [
        'category_id',
        'kind',
        'title',
        'slug',
        'description',
        'estimated_time',
        'cost',
        'unit',
        'schedule',
        'documentation',
        'icon',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'documentation' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($service) {
            if (empty($service->slug) && !empty($service->title)) {
                $base = Str::slug($service->title);
                $slug = $base;
                $i = 1;
                // Ensure uniqueness within the same category
                while (self::where('slug', $slug)
                    ->where('category_id', $service->category_id)
                    ->exists()
                ) {
                    $slug = $base . '-' . $i++;
                }
                $service->slug = $slug;
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function getKindLabelAttribute(): string
    {
        return $this->kind === self::KIND_PROCEDURE ? 'Trámite' : 'Servicio';
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
            ->withPivot('caption', 'alt_text', 'position')
            ->orderBy('position');
    }
}
