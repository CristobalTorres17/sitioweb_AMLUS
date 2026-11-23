<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleRevision extends Model
{
    protected $fillable = [
        'article_id',
        'user_id',
        'title',
        'content',
        'excerpt',
        'image_path',
        'data',
        'revision_number',
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: belongs to an Article
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Relationship: belongs to a User (who created the revision)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a new revision from an article's current state.
     *
     * @param Article $article
     * @param int|null $userId
     * @return ArticleRevision
     */
    public static function createFromArticle(Article $article, ?int $userId = null): ArticleRevision
    {
        // Get the highest revision number for this article
        $latestRevision = static::where('article_id', $article->id)
            ->orderByDesc('revision_number')
            ->first();

        $nextRevisionNumber = $latestRevision ? $latestRevision->revision_number + 1 : 1;

        // Capture full article snapshot as JSON
        $snapshot = [
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'content' => $article->content,
            'excerpt' => $article->excerpt,
            'category_id' => $article->category_id,
            'image_path' => $article->image_path,
            'is_published' => $article->is_published,
            'published_at' => $article->published_at,
            'created_at' => $article->created_at,
            'updated_at' => $article->updated_at,
        ];

        return static::create([
            'article_id' => $article->id,
            'user_id' => $userId,
            'title' => $article->title,
            'content' => $article->content,
            'excerpt' => $article->excerpt,
            'image_path' => $article->image_path,
            'data' => $snapshot,
            'revision_number' => $nextRevisionNumber,
        ]);
    }

    /**
     * Restore an article to this revision's state.
     *
     * @return Article
     */
    public function restore(): Article
    {
        $article = $this->article;

        if (!$this->data) {
            throw new \Exception('Revision data is empty. Cannot restore.');
        }

        $data = $this->data;

        // Restore main fields
        $article->update([
            'title' => $data['title'] ?? null,
            'slug' => $data['slug'] ?? null,
            'content' => $data['content'] ?? null,
            'excerpt' => $data['excerpt'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'image_path' => $data['image_path'] ?? null,
            'is_published' => $data['is_published'] ?? false,
            'published_at' => $data['published_at'] ?? null,
        ]);

        return $article;
    }
}
