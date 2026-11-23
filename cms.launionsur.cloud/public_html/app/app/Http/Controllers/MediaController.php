<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\MediaTag;
use App\Models\MediaCollection;
use App\Services\ImageProcessingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\MediaStoreRequest;
use App\Http\Requests\MediaUpdateRequest;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class MediaController extends Controller
{
    public function __construct(private ImageProcessingService $imageService) {}

    /**
     * Display gallery of media items (Admin view)
     */
    public function index(Request $request): Response|JsonResponse
    {
        $this->authorize('viewAny', Media::class);
        $perPage = (int) $request->get('per_page', 15);
        $perPage = min($perPage, 100);
        $search = $request->get('search', '');
        $tags = $request->get('tags', []);
        $context = $request->get('context', ''); // articles|banners|services|cms_blocks|events|unused
        $collectionId = $request->get('collection_id'); // folder filter

        $query = Media::query();

        // Search by filename or original_filename
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('filename', 'like', "%{$search}%")
                    ->orWhere('original_filename', 'like', "%{$search}%");
            });
        }

        // Filter by tags (if provided)
        if (!empty($tags) && is_array($tags)) {
            $query->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('media_tags.id', $tags);
            }, '=', count($tags));
        }

        // Filter by usage context
        if ($context === 'articles') {
            $query->whereHas('articles');
        } elseif ($context === 'banners') {
            $query->whereHas('banners');
        } elseif ($context === 'services') {
            $query->whereHas('services');
        } elseif ($context === 'cms_blocks') {
            $query->whereHas('cmsBlocks');
        } elseif ($context === 'events') {
            $query->where(function ($q) {
                $q->whereHas('events')
                    ->orWhereHas('featuredInEvents');
            });
        } elseif ($context === 'unused') {
            $query->doesntHave('articles')
                ->doesntHave('banners')
                ->doesntHave('services')
                ->doesntHave('cmsBlocks')
                ->doesntHave('events')
                ->doesntHave('featuredInEvents');
        }

        // Filter by collection (folder)
        if ($collectionId !== null && $collectionId !== '') {
            $query->where('collection_id', (int) $collectionId);
        }

        // Execute query with usage counts and eager-loaded relations (no N+1)
        $media = $query->with(['tags', 'user:id,name', 'collection:id,name,color,icon'])
            ->withCount([
                'articles',
                'banners',
                'services',
                'cmsBlocks as cms_blocks_count',
                'events',
                'featuredInEvents as featured_events_count',
            ])
            ->latest()
            ->paginate($perPage);

        $formattedMedia = $media->map(function ($item) {
            $articlesCount  = $item->articles_count ?? 0;
            $bannersCount   = $item->banners_count ?? 0;
            $servicesCount  = $item->services_count ?? 0;
            $blocksCount    = $item->cms_blocks_count ?? 0;
            $eventsCount    = $item->events_count ?? 0;
            $featuredCount  = $item->featured_events_count ?? 0;

            return [
                'id' => $item->id,
                'filename' => $item->filename,
                'path' => 'media/original/' . $item->filename,
                'original_filename' => $item->original_filename,
                'mime_type' => $item->mime_type,
                'size' => $item->size,
                'width' => $item->width,
                'height' => $item->height,
                'disk' => $item->disk,
                'is_active' => $item->is_active,
                'urls' => $this->imageService->getVariantUrls('media', $item->filename),
                'metadata' => $item->metadata,
                'tags' => $item->tags->map(fn($t) => ['id' => $t->id, 'name' => $t->name])->toArray(),
                'user' => $item->user?->only(['id', 'name']),
                'created_at' => $item->created_at?->toIso8601String(),
                'collection' => $item->collection
                    ? ['id' => $item->collection->id, 'name' => $item->collection->name, 'color' => $item->collection->color]
                    : null,
                'usage_counts' => [
                    'articles'       => $articlesCount,
                    'banners'        => $bannersCount,
                    'services'       => $servicesCount,
                    'cms_blocks'     => $blocksCount,
                    'events'         => $eventsCount + $featuredCount,
                    'total'          => $articlesCount + $bannersCount + $servicesCount + $blocksCount + $eventsCount + $featuredCount,
                ],
            ];
        });

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $formattedMedia,
                'pagination' => [
                    'total' => $media->total(),
                    'per_page' => $media->perPage(),
                    'current_page' => $media->currentPage(),
                    'last_page' => $media->lastPage(),
                ],
            ]);
        }

        //dd($formattedMedia);
        // Get all tags for filter dropdown
        $allTags = MediaTag::orderBy('name')->get(['id', 'name', 'color']);

        return Inertia::render('Media/Gallery', [
            'media' => $formattedMedia,
            'pagination' => [
                'total' => $media->total(),
                'per_page' => $media->perPage(),
                'current_page' => $media->currentPage(),
                'last_page' => $media->lastPage(),
            ],
            'filters' => [
                'search' => $search,
                'tags' => $tags,
                'context' => $context,
                'collection_id' => $collectionId ? (int) $collectionId : null,
            ],
            'availableTags' => $allTags,
            'collectionsTree' => MediaCollection::tree(),
        ]);
    }

    /**
     * Show create form (for modal or dedicated page)
     */
    public function create(): Response
    {
        $this->authorize('create', Media::class);
        $tags = MediaTag::orderBy('name')->get(['id', 'name', 'color']);

        return Inertia::render('Media/Create', [
            'availableTags' => $tags,
            'collectionsTree' => MediaCollection::tree(),
        ]);
    }

    /**
     * Store uploaded media
     */
    public function store(MediaStoreRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', Media::class);
        $validated = $request->validated();

        $files = $request->hasFile('images') ? $request->file('images') : [$request->file('image')];
        $files = array_values(array_filter(is_array($files) ? $files : [$files]));

        if ($files === []) {
            return redirect()
                ->back()
                ->with('error', 'No se encontraron imágenes para subir.');
        }

        $uploadedCount = 0;

        try {
            foreach ($files as $index => $file) {
                $itemTitle = trim((string) ($validated['titles'][$index] ?? $validated['title'] ?? ''));
                $itemDescription = trim((string) ($validated['descriptions'][$index] ?? $validated['caption'] ?? ''));
                $itemAltText = trim((string) ($validated['alt_texts'][$index] ?? $validated['alt_text'] ?? ''));

                // Validate and process each image independently
                $result = $this->imageService->process($file, 'media');
                $metadata = array_merge(
                    is_array($result['metadata']) ? $result['metadata'] : [],
                    array_filter([
                        'caption' => $itemDescription,
                        'alt_text' => $itemAltText,
                    ], fn($v) => $v !== null && $v !== ''),
                );

                $media = Media::create([
                    'filename' => $result['filename'],
                    'original_filename' => $itemTitle !== '' ? $itemTitle : $file->getClientOriginalName(),
                    'path' => "media/{$result['filename']}",
                    'variants' => $result['variants'] ?? null,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'width' => $result['metadata']['width'] ?? null,
                    'height' => $result['metadata']['height'] ?? null,
                    'disk' => $result['disk'] ?? 'public',
                    'user_id' => $request->user()?->id,
                    'collection_id' => $validated['collection_id'] ?? null,
                    'is_active' => true,
                    'metadata' => $metadata,
                    'usage_count' => 0,
                ]);

                if (!empty($validated['tags'])) {
                    $media->tags()->attach($validated['tags']);
                }

                $uploadedCount++;
            }

            return redirect()
                ->route('media.index')
                ->with('success', $uploadedCount === 1
                    ? 'Image uploaded successfully'
                    : $uploadedCount . ' images uploaded successfully');
        } catch (\Exception $e) {
            // Detect if this is a dimension validation error or another type
            $message = $e->getMessage();
            $isDimensionError = str_contains($message, 'dimension') || str_contains($message, 'pixel');

            // For AJAX requests (XHR), return JSON
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'ValidationError',
                    'message' => $message,
                    'is_dimension_error' => $isDimensionError,
                ], 422);
            }

            // For form submissions, redirect with detailed error
            return redirect()
                ->back()
                ->with('error', 'Failed to upload image: ' . $message);
        }
    }

    /**
     * Show edit form
     */
    public function edit(Int $id): Response
    {
        $media = Media::with('tags', 'collection')->findOrFail($id);

        //dd($media);
        $this->authorize('view', $media);
        $tags = MediaTag::orderBy('name')->get(['id', 'name', 'color']);
        $attachedTagIds = $media->tags()->pluck('media_tags.id')->toArray();


        return Inertia::render('Media/Edit', [
            'media' => [
                'id' => $media->id,
                'filename' => $media->filename,
                'original_filename' => $media->original_filename,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'width' => $media->width,
                'height' => $media->height,
                'disk' => $media->disk,
                'is_active' => $media->is_active,
                'collection_id' => $media->collection_id,
                'urls' => $this->imageService->getVariantUrls('media', $media->filename),
                'metadata' => $media->metadata,
                'variants' => $media->variants,
                'used_in' => $media->usedIn(),
                'tags' => $attachedTagIds,
                'created_at' => $media->created_at?->toIso8601String(),
            ],
            'usedIn' => $media->usedIn(),
            'availableTags' => $tags,
            'collectionsTree' => MediaCollection::tree(),
        ]);
    }

    /**
     * Update media metadata
     */
    public function update(MediaUpdateRequest $request, Media $media): RedirectResponse
    {
        $this->authorize('update', $media);
        $validated = $request->validated();

        try {
            // Update media (including metadata)
            $meta = is_array($media->metadata) ? $media->metadata : [];
            $meta = array_merge($meta, array_filter([
                'alt_text' => $validated['alt_text'] ?? null,
                'caption' => $validated['caption'] ?? null,
            ], fn($v) => $v !== null && $v !== ''));

            $media->update([
                'is_active' => $validated['is_active'] ?? $media->is_active,
                'original_filename' => $validated['title'] ?? $media->original_filename,
                'collection_id' => $validated['collection_id'] ?? $media->collection_id,
                'metadata' => $meta,
            ]);

            // Sync tags
            if (isset($validated['tags'])) {
                $media->tags()->sync($validated['tags']);
            }

            return redirect()
                ->route('media.index')
                ->with('success', 'Media updated successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update media: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete media
     */
    public function destroy(Media $media): RedirectResponse
    {
        $this->authorize('delete', $media);
        try {
            // Just soft delete - variants remain in storage for recovery
            $media->delete();

            return redirect()
                ->route('media.index')
                ->with('success', 'Media deleted successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete media: ' . $e->getMessage());
        }
    }

    /**
     * Blade fallback gallery for environments without Inertia frontend.
     */
    public function galleryBlade(Request $request)
    {
        $this->authorize('viewAny', Media::class);

        $perPage = (int) $request->get('per_page', 24);
        $perPage = min(max($perPage, 6), 100);

        $search = $request->get('search');

        $query = Media::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('filename', 'like', "%{$search}%")
                    ->orWhere('original_filename', 'like', "%{$search}%");
            });
        }

        $media = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.media.gallery', compact('media', 'search'));
    }
}
