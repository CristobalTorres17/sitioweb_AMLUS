<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\ArticleRevision;
use App\Models\Category;
use App\Models\CmsBlock;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ArticleController extends Controller
{
    public function index()
    {
        // Get filter parameters with defaults
        $perPage = request()->get('perPage', 12);
        $search = request()->get('search');
        $status = request()->get('status', 'all'); // all, published, draft
        $dateFrom = request()->get('date_from');
        $dateTo = request()->get('date_to');
        $categoryId = request()->get('category_id');
        $sort = request()->get('sort', 'published_at'); // published_at, created_at, title

        // Build query with filters
        $query = Article::withTrashed()
            ->with('category')
            ->whereSearch($search)
            ->whereStatus($status)
            ->whereDateRange($dateFrom, $dateTo)
            ->whereCategory($categoryId ? intval($categoryId) : null)
            ->orderBySort($sort);


        $articles = $query
            ->paginate($perPage)
            ->through(function ($a) {
                return [
                    'id' => $a->id,
                    'title' => $a->title,
                    'slug' => $a->slug,
                    'excerpt' => $a->excerpt,
                    'is_published' => (bool) $a->is_published,
                    'published_at' => $a->published_at ? $a->published_at->toDateTimeString() : null,
                    'deleted_at' => $a->deleted_at ? $a->deleted_at->toDateTimeString() : null,
                    'category' => $a->category ? ['id' => $a->category->id, 'name' => $a->category->name] : null,
                ];
            });

        //dd($articles);

        // Get all categories for filter dropdown
        $categories = Category::where('type', Category::TYPE_ARTICLE)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();


        return Inertia::render('Articles/Index', [
            'articles' => $articles,
            'categories' => $categories,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'category_id' => $categoryId,
                'sort' => $sort,
            ],
        ]);
    }

    public function create()
    {
        $categories = Category::where('type', Category::TYPE_ARTICLE)->orderBy('position')->get(['id', 'name', 'slug', 'color', 'icon'])->toArray();

        return Inertia::render('Articles/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255',
            'excerpt'          => 'nullable|string|max:1000',
            'content'          => 'nullable|string',
            'category_id'      => 'nullable|exists:categories,id',
            'status'           => 'sometimes|in:draft,published,archived',
            'published_at'     => 'nullable|date',
            'is_featured'      => 'sometimes|boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'image_path'       => 'nullable|string|max:255',
            'image'            => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $data['status'] = $data['status'] ?? 'draft';
        $data['is_published'] = $data['status'] === 'published';

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if (empty($data['image_path'])) {
            $data['image_path'] = $this->resolveImagePathFromMedia($request->input('media'));
        }

        if (empty($data['image_path'])) {
            throw ValidationException::withMessages([
                'image_path' => 'Selecciona una imagen principal para el artículo.',
            ]);
        }

        if (!empty($data['content'])) {
            $data['content'] = app('purifier')->clean($data['content']);
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('articles', 'public');
        }

        $article = Article::create($data);

        if ($request->has('media') && is_array($request->input('media'))) {
            $this->syncMedia($article, $request->input('media'));
        }

        return redirect()->route('articles.index')->with('success', 'Article created.');
    }

    public function edit($id)
    {
        $article = Article::with(['category', 'media'])->findOrFail($id);
        $articlePageSlug = 'article:' . $article->slug;
        $articleBlocks = CmsBlock::with(['pages' => fn($q) => $q->where('page', $articlePageSlug)])
            ->whereHas('pages', fn($q) => $q->where('page', $articlePageSlug))
            ->get(['id', 'name', 'slug', 'zone', 'template_key', 'is_active'])
            ->map(fn($block) => [
                'id' => $block->id,
                'name' => $block->name,
                'slug' => $block->slug,
                'zone' => $block->zone,
                'template_key' => $block->template_key,
                'is_active' => (bool) $block->is_active,
                'position' => $block->pages->first()?->position ?? 0,
            ])
            ->sortBy('position')
            ->values();

        $categories = Category::where('type', Category::TYPE_ARTICLE)->orderBy('position')->get(['id', 'name', 'slug', 'color', 'icon'])->toArray();

        return Inertia::render('Articles/Edit', [
            'article' => array_merge($article->toArray(), [
                'status'    => $article->status ?? ($article->is_published ? 'published' : 'draft'),
            ]),
            'categories' => $categories,
            'previewUrl' => $article->image_path ? Storage::url($article->image_path) : null,
            'articleBlocks' => $articleBlocks,
            'articlePageSlug' => $articlePageSlug,
        ]);
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255',
            'excerpt'          => 'nullable|string|max:1000',
            'content'          => 'nullable|string',
            'category_id'      => 'nullable|exists:categories,id',
            'status'           => 'sometimes|in:draft,published,archived',
            'published_at'     => 'nullable|date',
            'is_featured'      => 'sometimes|boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'image_path'       => 'nullable|string|max:255',
            'image'            => 'nullable|file|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $data['status'] = $data['status'] ?? $article->status ?? 'draft';
        $data['is_published'] = $data['status'] === 'published';

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = $article->published_at ?? now();
        }

        if (empty($data['image_path'])) {
            $data['image_path'] = $this->resolveImagePathFromMedia($request->input('media'));
        }

        // Fall back to the already-stored image_path on the article
        if (empty($data['image_path'])) {
            $data['image_path'] = $article->image_path;
        }

        if (empty($data['image_path'])) {
            throw ValidationException::withMessages([
                'image_path' => 'Selecciona una imagen principal para el artículo.',
            ]);
        }

        if (!empty($data['content'])) {
            $data['content'] = app('purifier')->clean($data['content']);
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('articles', 'public');
        }

        $article->update($data);

        if ($request->has('media') && is_array($request->input('media'))) {
            $this->syncMedia($article, $request->input('media'));
        }

        ArticleRevision::createFromArticle($article, $request->user()?->getKey());

        return redirect()->route('articles.index')->with('success', 'Article updated.');
    }

    /**
     * Synchronize media pivot rows for an article.
     * Expected format: array of { media_id, is_primary, position, caption, alt_text }
     * This will upsert pivot rows and ensure at most one is_primary.
     *
     * @param Article $article
     * @param array $mediaArray
     * @return void
     */
    protected function syncMedia(Article $article, array $mediaArray)
    {
        DB::transaction(function () use ($article, $mediaArray) {
            $syncData = [];
            $primaryMediaId = null;

            foreach ($mediaArray as $i => $m) {
                $mediaId = isset($m['media_id']) ? intval($m['media_id']) : (isset($m['id']) ? intval($m['id']) : null);
                if (!$mediaId) {
                    continue;
                }

                $isPrimary = !empty($m['is_primary']) ? true : false;
                if ($isPrimary) {
                    $primaryMediaId = $mediaId;
                }

                $syncData[$mediaId] = [
                    'is_primary' => $isPrimary,
                    'position' => isset($m['position']) ? intval($m['position']) : $i,
                    'caption' => isset($m['caption']) ? $m['caption'] : null,
                    'alt_text' => isset($m['alt_text']) ? $m['alt_text'] : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // perform sync (this replaces pivot set for the article)
            $article->media()->sync($syncData);

            // enforce single primary: if a media was marked primary, clear others and set it
            if ($primaryMediaId) {
                DB::table('article_media')->where('article_id', $article->id)->update(['is_primary' => false]);

                DB::table('article_media')->where('article_id', $article->id)->where('media_id', $primaryMediaId)->update(['is_primary' => true]);

                // also update legacy image_path on article for backward compatibility
                $primaryMedia = \App\Models\Media::find($primaryMediaId);
                if ($primaryMedia && isset($primaryMedia->path)) {
                    $article->image_path = $primaryMedia->path;
                    $article->save();
                }
            } elseif (!$primaryMediaId && count($syncData) > 0) {
                // fallback: if no primary was specified, mark the first synced media as primary
                $firstMediaId = array_key_first($syncData);
                if ($firstMediaId) {
                    DB::table('article_media')->where('article_id', $article->id)->update(['is_primary' => false]);
                    DB::table('article_media')->where('article_id', $article->id)->where('media_id', $firstMediaId)->update(['is_primary' => true]);

                    $primaryMedia = \App\Models\Media::find($firstMediaId);
                    if ($primaryMedia && isset($primaryMedia->path)) {
                        $article->image_path = $primaryMedia->path;
                        $article->save();
                    }
                }
            }
        });
    }

    private function resolveImagePathFromMedia(mixed $mediaArray): ?string
    {
        if (!is_array($mediaArray) || empty($mediaArray)) {
            return null;
        }

        // First pass: primary item with path already present
        foreach ($mediaArray as $media) {
            if (!is_array($media)) {
                continue;
            }

            if (!empty($media['is_primary']) && !empty($media['path'])) {
                return (string) $media['path'];
            }
        }

        // Second pass: primary item resolved via media_id DB lookup
        foreach ($mediaArray as $media) {
            if (!is_array($media)) {
                continue;
            }

            if (!empty($media['is_primary']) && !empty($media['media_id'])) {
                $m = \App\Models\Media::find(intval($media['media_id']));
                if ($m && !empty($m->path)) {
                    return (string) $m->path;
                }
            }
        }

        // Third pass: any item with a path
        foreach ($mediaArray as $media) {
            if (!is_array($media)) {
                continue;
            }

            if (!empty($media['path'])) {
                return (string) $media['path'];
            }
        }

        // Fourth pass: any item resolved via media_id DB lookup
        foreach ($mediaArray as $media) {
            if (!is_array($media)) {
                continue;
            }

            if (!empty($media['media_id'])) {
                $m = \App\Models\Media::find(intval($media['media_id']));
                if ($m && !empty($m->path)) {
                    return (string) $m->path;
                }
            }
        }

        return null;
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Article deleted.');
    }

    public function restore($id)
    {
        $article = Article::withTrashed()->findOrFail($id);
        if ($article->trashed()) {
            $article->restore();
        }

        return redirect()->route('articles.index')->with('success', 'Article restored.');
    }

    /**
     * Auto-save draft endpoint.
     * Accepts partial data and relaxed validation for drafts.
     * Expected fields: title, slug, excerpt, content, category_id, is_published, published_at, media
     * All fields are optional except when explicitly required.
     *
     * POST /articles/draft (create new draft)
     * POST /articles/{article}/draft (update existing draft)
     *
     * @param Request $request
     * @param Article|null $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function draft(Request $request, ?Article $article = null)
    {
        // Relaxed validation for drafts
        $data = $request->validate([
            'title'            => 'nullable|string|max:255',
            'slug'             => 'nullable|string|max:255',
            'excerpt'          => 'nullable|string|max:1000',
            'content'          => 'nullable|string',
            'category_id'      => 'nullable|exists:categories,id',
            'status'           => 'sometimes|in:draft,published,archived',
            'is_featured'      => 'sometimes|boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'published_at'     => 'nullable|date',
            'image_path'       => 'nullable|string|max:255',
            'media'            => 'array',
        ]);

        if (isset($data['status'])) {
            $data['is_published'] = $data['status'] === 'published';
        }

        if (empty($data['image_path'])) {
            $data['image_path'] = $this->resolveImagePathFromMedia($request->input('media'));
        }

        // Sanitize content to prevent XSS
        if (!empty($data['content'])) {
            $data['content'] = app('purifier')->clean($data['content']);
        }

        if (!$article) {
            // Create new draft (only if at least title is provided)
            if (empty($data['title'])) {
                $data['title'] = 'Draft Article';
            }
            $article = Article::create($data);
        } else {
            // Update existing article (partial update)
            $article->update($data);
        }

        // Sync media if provided
        if ($request->has('media') && is_array($request->input('media'))) {
            $this->syncMedia($article, $request->input('media'));
        }

        return response()->json([
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'content' => $article->content,
            'excerpt' => $article->excerpt,
            'category_id' => $article->category_id,
            'is_published' => (bool) $article->is_published,
            'published_at' => $article->published_at ? $article->published_at->toDateTimeString() : null,
            'image_path' => $article->image_path,
            'updated_at' => $article->updated_at->toDateTimeString(),
            'message' => 'Draft saved successfully.',
        ]);
    }

    /**
     * Get all revisions for an article.
     * Returns list of revisions with metadata for the RevisionHistory UI.
     *
     * GET /articles/{article}/revisions
     *
     * @param Article $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function revisions(Article $article)
    {
        $revisions = $article->revisions()->get()->map(function ($revision) {
            return [
                'id' => $revision->id,
                'article_id' => $revision->article_id,
                'revision_number' => $revision->revision_number,
                'title' => $revision->title,
                'excerpt' => $revision->excerpt ? substr($revision->excerpt, 0, 100) . (strlen($revision->excerpt) > 100 ? '...' : '') : null,
                'image_path' => $revision->image_path,
                'user_id' => $revision->user_id,
                'user_name' => $revision->user ? $revision->user->name : 'System',
                'created_at' => $revision->created_at->toDateTimeString(),
                'created_at_human' => $revision->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'article_id' => $article->id,
            'revisions' => $revisions,
        ]);
    }

    /**
     * Restore an article to a specific revision.
     *
     * POST /articles/{article}/revisions/{revision}/restore
     *
     * @param Article $article
     * @param ArticleRevision $revision
     * @return \Illuminate\Http\JsonResponse
     */
    public function restoreRevision(Request $request, Article $article, ArticleRevision $revision)
    {
        // Verify the revision belongs to this article
        if ($revision->article_id !== $article->id) {
            return response()->json([
                'message' => 'Revision does not belong to this article.',
            ], 400);
        }

        try {
            // Restore the article to this revision's state
            $revision->restore();

            // Create a new revision to track this restoration
            ArticleRevision::createFromArticle($article, $request->user()?->getKey());

            return response()->json([
                'message' => 'Article restored to revision ' . $revision->revision_number . ' successfully.',
                'article' => [
                    'id' => $article->id,
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'content' => $article->content,
                    'excerpt' => $article->excerpt,
                    'image_path' => $article->image_path,
                    'is_published' => (bool) $article->is_published,
                    'published_at' => $article->published_at ? $article->published_at->toDateTimeString() : null,
                    'updated_at' => $article->updated_at->toDateTimeString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to restore revision: ' . $e->getMessage(),
            ], 500);
        }
    }
}
