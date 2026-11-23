<?php

namespace App\Http\Controllers;

use App\Models\CmsBlock;
use App\Models\Page;
use App\Models\PageVersion;
use App\Http\Requests\PageStoreRequest;
use App\Http\Requests\PageUpdateRequest;
use App\Models\User;
use App\Services\SlugGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Purifier;

class PageController extends Controller
{
    protected SlugGeneratorService $slugService;

    public function __construct(SlugGeneratorService $slugService)
    {
        $this->slugService = $slugService;
        $this->middleware('auth');
    }

    /**
     * Display a listing of pages.
     */
    public function index()
    {
        $this->authorize('viewAny', Page::class);
        $perPage = request()->get('perPage', 15);
        $search = request()->get('search');
        $published = request()->get('published', 'all'); // all, published, draft

        $query = Page::withTrashed()
            ->with('creator')
            ->orderBy('created_at', 'desc');

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Published filter
        if ($published === 'published') {
            $query->where('is_published', true);
        } elseif ($published === 'draft') {
            $query->where('is_published', false);
        }

        $pages = $query->paginate($perPage)
            ->through(function ($page) {
                return [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'description' => Str::limit($page->description, 100),
                    'is_published' => (bool) $page->is_published,
                    'published_at' => $page->published_at?->toDateTimeString(),
                    'created_at' => $page->created_at->toDateTimeString(),
                    'updated_at' => $page->updated_at->toDateTimeString(),
                    'deleted_at' => $page->deleted_at?->toDateTimeString(),
                    'creator' => $page->creator ? [
                        'id' => $page->creator->id,
                        'name' => $page->creator->name,
                    ] : null,
                ];
            });

        return Inertia::render('Pages/Index', [
            'pages' => $pages,
            'filters' => [
                'search' => $search,
                'published' => $published,
            ],
        ]);
    }

    /**
     * Show the form for creating a new page.
     */
    public function create()
    {
        $this->authorize('create', Page::class);
        return Inertia::render('Pages/Create');
    }

    /**
     * Store a newly created page in storage.
     */
    public function store(PageStoreRequest $request)
    {
        $this->authorize('create', Page::class);
        $data = $request->validated();

        // Validate slug if provided, otherwise generate
        $slug = $data['slug'] ?? '';
        if (!$slug) {
            $slug = $this->slugService->generateSlug($data['title']);
        } else {
            if (!$this->slugService->isValidSlug($slug)) {
                return back()->withErrors(['slug' => 'Slug must be lowercase alphanumeric with hyphens (3-255 chars)']);
            }
        }

        // Sanitize HTML content
        $data['content'] = Purifier::clean($data['content']);
        $data['slug'] = $slug;
        $data['created_by'] = auth()->id();

        // Create page
        $page = Page::create($data);

        // Create first version
        $page->createVersion($data, auth()->id(), 'Initial version');

        return redirect()->route('pages.edit', $page->id)->with('success', 'Page created successfully.');
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(Page $page)
    {
        $this->authorize('view', $page);
        $page->load('creator', 'versions', 'ogImage');

        $pageBlocks = CmsBlock::with(['pages' => fn($query) => $query->where('page', $page->slug)])
            ->whereHas('pages', fn($query) => $query->where('page', $page->slug))
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

        return Inertia::render('Pages/Edit', [
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'content' => $page->content,
                'description' => $page->description,
                'meta_description' => $page->meta_description,
                'meta_keywords' => $page->meta_keywords,
                'og_image_media_id' => $page->og_image_media_id,
                'is_published' => (bool) $page->is_published,
                'published_at' => $page->published_at?->toDateTimeString(),
                'created_at' => $page->created_at->toDateTimeString(),
                'updated_at' => $page->updated_at->toDateTimeString(),
                'creator' => $page->creator ? [
                    'id' => $page->creator->id,
                    'name' => $page->creator->name,
                ] : null,
                'versions_count' => $page->versions->count(),
                'og_image' => $page->ogImage ? [
                    'id' => $page->ogImage->id,
                    'filename' => $page->ogImage->filename,
                ] : null,
            ],
            'pageBlocks' => $pageBlocks,
            'pageBlocksSlug' => $page->slug,
        ]);
    }

    /**
     * Update the specified page in storage.
     */
    public function update(PageUpdateRequest $request, Page $page)
    {
        $this->authorize('update', $page);
        $data = $request->validated();

        // Validate slug if provided
        if ($data['slug'] && $data['slug'] !== $page->slug) {
            if (!$this->slugService->isValidSlug($data['slug'])) {
                return back()->withErrors(['slug' => 'Slug must be lowercase alphanumeric with hyphens (3-255 chars)']);
            }
        }

        // Sanitize HTML content
        $data['content'] = Purifier::clean($data['content']);

        // Update page
        $page->update($data);

        // Create new version
        $page->createVersion($data, auth()->id(), 'Updated by ' . auth()->user()->name);

        return redirect()->route('pages.edit', $page->id)->with('success', 'Page updated successfully.');
    }

    /**
     * Remove the specified page from storage (soft delete).
     */
    public function destroy(Page $page)
    {
        $this->authorize('delete', $page);
        $page->delete();

        return redirect()->route('pages.index')->with('success', 'Page deleted successfully.');
    }

    /**
     * Restore a soft-deleted page.
     */
    public function restore(Page $page)
    {
        $this->authorize('restore', $page);
        $page->restore();

        return redirect()->route('pages.index')->with('success', 'Page restored successfully.');
    }

    /**
     * Publish a page.
     */
    public function publish(Request $request, Page $page)
    {
        $this->authorize('update', $page);
        $page->publish();

        return redirect()->route('pages.edit', $page->id)->with('success', 'Page published successfully.');
    }

    /**
     * Unpublish a page.
     */
    public function unpublish(Request $request, Page $page)
    {
        $this->authorize('update', $page);
        $page->unpublish();

        return redirect()->route('pages.edit', $page->id)->with('success', 'Page unpublished successfully.');
    }
}
