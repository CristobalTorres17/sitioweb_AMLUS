<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The model's default attribute values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_published' => false,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'description',
        'meta_description',
        'meta_keywords',
        'og_image_media_id',
        'is_published',
        'published_at',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the versions of this page.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(PageVersion::class);
    }

    /**
     * Get the creator of this page.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the OG image media.
     */
    public function ogImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'og_image_media_id');
    }

    /**
     * Get the latest version of this page.
     */
    public function latestVersion(): ?PageVersion
    {
        return $this->versions()
            ->orderByDesc('version_number')
            ->first();
    }

    /**
     * Create a new version of this page.
     */
    public function createVersion(array $data, $userId = null, $reason = null): PageVersion
    {
        $latestVersion = $this->latestVersion();
        $versionNumber = $latestVersion ? $latestVersion->version_number + 1 : 1;

        return $this->versions()->create([
            'title' => $data['title'] ?? $this->title,
            'content' => $data['content'] ?? $this->content,
            'description' => $data['description'] ?? $this->description,
            'meta_description' => $data['meta_description'] ?? $this->meta_description,
            'meta_keywords' => $data['meta_keywords'] ?? $this->meta_keywords,
            'og_image_media_id' => $data['og_image_media_id'] ?? $this->og_image_media_id,
            'was_published' => (bool) ($data['is_published'] ?? $this->is_published ?? false),
            'version_number' => $versionNumber,
            'change_reason' => $reason,
            'created_by' => $userId,
        ]);
    }

    /**
     * Publish the page.
     */
    public function publish(): void
    {
        $this->update([
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    /**
     * Unpublish the page.
     */
    public function unpublish(): void
    {
        $this->update([
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    /**
     * Check if page is published.
     */
    public function isPublished(): bool
    {
        return $this->is_published && $this->published_at !== null;
    }
}
