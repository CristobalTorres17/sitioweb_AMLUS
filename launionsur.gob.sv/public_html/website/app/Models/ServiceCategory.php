<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCategory extends Model
{
    use SoftDeletes;

    public const TYPE_SERVICE = 'service';
    public const TYPE_PROCEDURE = 'procedure';

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'icon',
        'color',
        'is_active',
        'position',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position' => 'integer',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('service_type', function (Builder $builder) {
            $builder->where('type', self::TYPE_SERVICE);
        });

        static::creating(function (self $category): void {
            $category->type = $category->type ?: self::TYPE_SERVICE;
        });

        static::updating(function (self $category): void {
            $category->type = self::TYPE_SERVICE;
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'category_id')->orderBy('order');
    }
}
