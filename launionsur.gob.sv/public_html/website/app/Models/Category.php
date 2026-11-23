<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    public const TYPE_ARTICLE = 'article';
    public const TYPE_EVENT  = 'event';

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
        static::addGlobalScope('article_type', function (Builder $builder) {
            $builder->where('type', self::TYPE_ARTICLE);
        });

        static::creating(function (self $category): void {
            $category->type = $category->type ?: self::TYPE_ARTICLE;
        });

        static::updating(function (self $category): void {
            $category->type = self::TYPE_ARTICLE;
        });
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'category_id');
    }
}
