<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\MediaTag;
use App\Models\MediaCollection;
use App\Services\ImageProcessingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaApiController extends Controller
{
    protected ImageProcessingService $imageService;

    public function __construct()
    {
        $this->imageService = new ImageProcessingService();
    }

    /**
     * GET /api/media
     * Get all active media paginated, optionally filtered by collection_id.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 15);
        $perPage = min($perPage, 100);
        $collectionId = $request->get('collection_id');
        $search = trim((string) $request->get('search', ''));
        $tags = $request->get('tags', []);

        $query = Media::where('is_active', true);

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('filename', 'like', "%{$search}%")
                    ->orWhere('original_filename', 'like', "%{$search}%");
            });
        }

        if ($collectionId !== null && $collectionId !== '') {
            $query->where('collection_id', (int) $collectionId);
        }

        if (is_array($tags) && !empty($tags)) {
            $query->whereHas('tags', function ($builder) use ($tags) {
                $builder->whereIn('media_tags.id', array_map('intval', $tags));
            }, '=', count($tags));
        }

        $media = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $this->formatMediaCollection($media->items()),
            'pagination' => [
                'total' => $media->total(),
                'per_page' => $media->perPage(),
                'current_page' => $media->currentPage(),
                'last_page' => $media->lastPage(),
            ],
        ]);
    }

    /**
     * GET /api/media/collections
     * Returns the full folder tree (used by media pickers).
     */
    public function collections(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => MediaCollection::tree(),
        ]);
    }

    /**
     * GET /api/media/{id}
     * Get single media by ID
     */
    public function show(int $id): JsonResponse
    {
        $media = Media::where('is_active', true)->find($id);

        if (!$media) {
            return response()->json([
                'success' => false,
                'message' => 'Media not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatMedia($media),
        ]);
    }

    /**
     * GET /api/media/search?q={keyword}
     * Search media by filename
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->get('q', ''));
        $perPage = (int) $request->get('per_page', 15);
        $perPage = min($perPage, 100);

        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Search query must be at least 2 characters',
            ], 400);
        }

        $media = Media::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('filename', 'like', "%{$query}%")
                    ->orWhere('original_filename', 'like', "%{$query}%");
            })
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $this->formatMediaCollection($media->items()),
            'count' => $media->total(),
            'pagination' => [
                'total' => $media->total(),
                'per_page' => $media->perPage(),
                'current_page' => $media->currentPage(),
                'last_page' => $media->lastPage(),
            ],
        ]);
    }

    /**
     * GET /api/media/tags
    /**
     * GET /api/media/{id}/usage
     * Returns detailed usage info: which entities (articles, events, etc.) use this media.
     * This is called lazily by the admin modal, so N+1 is fine for a single item.
     */
    public function usage(int $id): JsonResponse
    {
        $media = Media::find($id);

        if (!$media) {
            return response()->json(['success' => false, 'message' => 'Media not found'], 404);
        }

        $usedIn = $media->usedIn();

        return response()->json([
            'success' => true,
            'data' => $usedIn,
        ]);
    }

    /**
     * GET /api/media/tags
     * Get all tags with media count
     */
    public function tags(Request $request): JsonResponse
    {
        $tags = MediaTag::withCount(['media' => function ($query) {
            $query->where('is_active', true);
        }])
            ->orderByDesc('media_count')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tags->map(function (MediaTag $tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                    'color' => $tag->color,
                    'media_count' => $tag->media_count,
                ];
            })->values(),
        ]);
    }

    /**
     * Format a single media item with variant URLs
     */
    protected function formatMedia(Media $media): array
    {
        $urls = $this->imageService->getVariantUrls('media', $media->filename);

        return [
            'id' => $media->id,
            'filename' => $media->filename,
            'original_filename' => $media->original_filename,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'is_active' => $media->is_active,
            'urls' => $urls,
            'metadata' => $media->metadata,
            'created_at' => $media->created_at?->toIso8601String(),
            'tags' => $media->tags()->pluck('name')->values(),
        ];
    }

    /**
     * Format a collection of media items
     */
    protected function formatMediaCollection(iterable $items): array
    {
        return array_map(fn($item) => $this->formatMedia($item), $items instanceof \Illuminate\Support\Collection ? $items->all() : $items);
    }
}
