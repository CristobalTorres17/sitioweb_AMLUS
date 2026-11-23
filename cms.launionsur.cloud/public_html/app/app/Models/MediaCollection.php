<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MediaCollection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'collectable_type',
        'collectable_id',
        'description',
        'color',
        'icon',
        'sort_order',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (MediaCollection $collection) {
            if (empty($collection->slug)) {
                $collection->slug = Str::slug($collection->name);
            }
        });

        static::updating(function (MediaCollection $collection) {
            if ($collection->isDirty('name') && !$collection->isDirty('slug')) {
                $collection->slug = Str::slug($collection->name);
            }
        });
    }

    /** Parent folder */
    public function parent()
    {
        return $this->belongsTo(MediaCollection::class, 'parent_id');
    }

    /** Direct child folders */
    public function children()
    {
        return $this->hasMany(MediaCollection::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    /** Media files inside this folder */
    public function media()
    {
        return $this->hasMany(Media::class, 'collection_id');
    }

    /** The entity (Event, Article, …) that auto-created this folder */
    public function collectable()
    {
        return $this->morphTo();
    }

    /** The admin user who created this folder */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Build a nested tree array from the database.
     * Loads the whole tree in 2 queries using eager-loading.
     */
    public static function tree(): array
    {
        // Load all non-deleted collections with media count in one query
        $all = static::withCount('media')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->keyBy('id');

        return static::buildBranch(null, $all);
    }

    private static function buildBranch(?int $parentId, $all): array
    {
        return $all->filter(fn($c) => $c->parent_id === $parentId)
            ->values()
            ->map(fn($c) => [
                'id'          => $c->id,
                'name'        => $c->name,
                'slug'        => $c->slug,
                'color'       => $c->color,
                'icon'        => $c->icon,
                'media_count' => $c->media_count,
                'collectable_type' => $c->collectable_type,
                'collectable_id'   => $c->collectable_id,
                'children'    => static::buildBranch($c->id, $all),
            ])
            ->toArray();
    }

    /**
     * Find or create the root folder for a given entity type name (e.g. 'Eventos').
     * Used when auto-creating sub-folders for new Events / Articles.
     */
    public static function findOrCreateRoot(string $name, string $color = '#6366f1', string $icon = 'folder'): static
    {
        return static::firstOrCreate(
            ['name' => $name, 'parent_id' => null, 'collectable_type' => null],
            ['color' => $color, 'icon' => $icon],
        );
    }
}
