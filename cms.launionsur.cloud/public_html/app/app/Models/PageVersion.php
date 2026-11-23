<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageVersion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'page_id',
        'title',
        'content',
        'description',
        'meta_description',
        'meta_keywords',
        'og_image_media_id',
        'was_published',
        'version_number',
        'change_reason',
        'restored_from',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'was_published' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the page that owns this version.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the creator of this version.
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
     * Restore this version to the page.
     */
    public function restoreToPage($userId = null): Page
    {
        // Update the page with this version's data
        $page = $this->page;
        $page->update([
            'title' => $this->title,
            'content' => $this->content,
            'description' => $this->description,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'og_image_media_id' => $this->og_image_media_id,
        ]);

        // Create a new version marking it as restored
        $newVersion = $page->createVersion(
            $page->toArray(),
            $userId,
            "Restored from version {$this->version_number}"
        );
        $newVersion->update(['restored_from' => "v{$this->version_number}"]);

        return $page;
    }

    /**
     * Check if this is the latest version.
     */
    public function isLatest(): bool
    {
        return $this->id === $this->page->latestVersion()?->id;
    }
}
