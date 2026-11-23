<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCategory extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_SERVICE = 'service';
    public const TYPE_TRAMITE = 'procedure';

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
        static::creating(function (self $category): void {
            $category->type = $category->type ?: self::TYPE_SERVICE;
        });
    }

    public static function managedTypes(): array
    {
        return [self::TYPE_SERVICE, self::TYPE_TRAMITE];
    }

    public static function typeForKind(string $kind): string
    {
        return $kind === Service::KIND_PROCEDURE ? self::TYPE_TRAMITE : self::TYPE_SERVICE;
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeForKind(Builder $query, string $kind): Builder
    {
        return $query->where('type', self::typeForKind($kind));
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'category_id')
            ->orderBy('order');
    }
}
