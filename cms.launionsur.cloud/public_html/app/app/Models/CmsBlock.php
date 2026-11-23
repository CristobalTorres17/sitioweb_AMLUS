<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CmsBlock extends Model
{
    use HasFactory, SoftDeletes;

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

    public static function booted()
    {
        static::creating(function ($block) {
            if (empty($block->slug) && !empty($block->name)) {
                $base = Str::slug($block->name);
                $slug = $base;
                $i = 2;
                while (self::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $block->slug = $slug;
            }
        });

        static::updating(function ($block) {
            if ($block->isDirty(['name', 'content', 'excerpt', 'template_key', 'template_data', 'zone'])) {
                // create revision from the original snapshot (pre-update)
                $original = $block->getOriginal();
                CmsBlockRevision::createFromSnapshot($original);
            }
        });
    }

    public function pages()
    {
        return $this->hasMany(CmsBlockPage::class, 'cms_block_id')
            ->orderBy('position');
    }

    public function media()
    {
        return $this->belongsToMany(Media::class, 'cms_block_media')
            ->withPivot(['position', 'caption', 'alt_text'])
            ->withTimestamps()
            ->orderBy('position');
    }

    public function revisions()
    {
        return $this->hasMany(CmsBlockRevision::class)
            ->orderByDesc('created_at');
    }

    public function latestRevision()
    {
        return $this->hasOne(CmsBlockRevision::class)
            ->orderByDesc('revision_number')
            ->limit(1);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForPage($query, $page)
    {
        return $query->whereHas('pages', function ($q) use ($page) {
            $q->where('cms_block_page.page', $page);
        });
    }
}
