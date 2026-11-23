<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventStoreRequest;
use App\Http\Requests\EventUpdateRequest;
use App\Models\Category;
use App\Models\CmsBlock;
use App\Models\EventGallery;
use App\Models\Media;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Event::class);

        $perPage = (int) $request->get('perPage', 12);
        $perPage = max(1, min($perPage, 100));

        $search = $request->get('search');
        $status = $request->get('status', 'all');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $featured = $request->get('featured');
        $sort = $request->get('sort', 'event_date');

        $query = Event::withTrashed()
            ->with(['author:id,name', 'featuredImage:id,filename,path'])
            ->withCount(['gallery', 'comments'])
            ->whereSearch($search)
            ->whereStatus($status)
            ->whereDateRange($dateFrom, $dateTo)
            ->orderBySort($sort);

        if ($featured !== null && $featured !== '' && $featured !== 'all') {
            $query->where('is_featured', filter_var($featured, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false);
        }

        $events = $query->paginate($perPage)->through(function (Event $event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'slug' => $event->slug,
                'description' => $event->description,
                'event_date' => $event->event_date?->toDateTimeString(),
                'event_date_end' => $event->event_date_end?->toDateTimeString(),
                'location' => $event->location,
                'organizer' => $event->organizer,
                'status' => $event->status,
                'is_featured' => (bool) $event->is_featured,
                'published_at' => $event->published_at?->toDateTimeString(),
                'deleted_at' => $event->deleted_at?->toDateTimeString(),
                'author' => $event->author ? ['id' => $event->author->id, 'name' => $event->author->name] : null,
                'featured_image' => $event->featuredImage ? [
                    'id' => $event->featuredImage->id,
                    'filename' => $event->featuredImage->filename,
                    'path' => $event->featuredImage->path,
                ] : null,
                'gallery_count' => $event->gallery_count,
                'comments_count' => $event->comments_count,
            ];
        });

        return Inertia::render('Events/Index', [
            'events' => $events,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'featured' => $featured,
                'sort' => $sort,
            ],
        ]);
    }

    public function create()
    {
        $this->authorize('create', Event::class);

        $categories = Category::withoutGlobalScope('article_type')
            ->where('type', Category::TYPE_EVENT)
            ->orderBy('position')
            ->get(['id', 'name', 'slug', 'color', 'icon']);
        //dd($categories);
        return Inertia::render('Events/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(EventStoreRequest $request)
    {
        $this->authorize('create', Event::class);

        $data = $request->validated();
        $data['content'] = isset($data['content']) && $data['content'] !== '' ? app('purifier')->clean($data['content']) : null;
        $data['author_id'] = auth()->user()?->id;
        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);

        if (empty($data['slug'])) {
            $data['slug'] = $this->makeUniqueSlug($data['title']);
        }

        if (($data['status'] ?? null) === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        Event::create($data);

        return redirect()->route('events.index')->with('success', 'Event created.');
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        $event->load(['author:id,name', 'featuredImage:id,filename,path', 'gallery.media', 'comments.user']);

        $eventPageSlug = 'event:' . $event->slug;
        $eventBlocks = CmsBlock::with(['pages' => fn($q) => $q->where('page', $eventPageSlug)])
            ->whereHas('pages', fn($q) => $q->where('page', $eventPageSlug))
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

        $categories = Category::withoutGlobalScope('article_type')
            ->where('type', Category::TYPE_EVENT)
            ->orderBy('position')
            ->get(['id', 'name', 'slug', 'color', 'icon']);

        return Inertia::render('Events/Edit', [
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'slug' => $event->slug,
                'description' => $event->description,
                'content' => $event->content,
                'event_date' => $event->event_date?->toDateTimeString(),
                'event_date_end' => $event->event_date_end?->toDateTimeString(),
                'location' => $event->location,
                'organizer' => $event->organizer,
                'featured_image_id' => $event->featured_image_id,
                'status' => $event->status,
                'published_at' => $event->published_at?->toDateTimeString(),
                'is_featured' => (bool) $event->is_featured,
                'meta_title' => $event->meta_title,
                'meta_description' => $event->meta_description,
                'metadata' => $event->metadata,
                'category_id' => $event->category_id,
                'author' => $event->author ? ['id' => $event->author->id, 'name' => $event->author->name] : null,
                'featured_image' => $event->featuredImage ? [
                    'id' => $event->featuredImage->id,
                    'filename' => $event->featuredImage->filename,
                    'path' => $event->featuredImage->path,
                ] : null,
                'gallery' => $event->gallery->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'media_id' => $item->media_id,
                        'position' => $item->position,
                        'caption' => $item->caption,
                        'media' => $item->media ? [
                            'id' => $item->media->id,
                            'filename' => $item->media->filename,
                            'path' => $item->media->path,
                        ] : null,
                    ];
                })->values(),
            ],
            'eventBlocks' => $eventBlocks,
            'eventPageSlug' => $eventPageSlug,
            'categories' => $categories,
        ]);
    }

    public function update(EventUpdateRequest $request, Event $event)
    {
        $this->authorize('update', $event);

        $data = $request->validated();
        $data['content'] = isset($data['content']) && $data['content'] !== '' ? app('purifier')->clean($data['content']) : null;
        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);

        if (empty($data['slug'])) {
            $data['slug'] = $this->makeUniqueSlug($data['title'], $event->id);
        }

        if (($data['status'] ?? null) === 'published' && empty($data['published_at'])) {
            $data['published_at'] = $event->published_at ?? now();
        }

        $event->update($data);

        return redirect()->route('events.index')->with('success', 'Event updated.');
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        $event->delete();

        return redirect()->route('events.index')->with('success', 'Event deleted.');
    }

    public function syncGallery(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $data = $request->validate([
            'media' => 'required|array',
            'media.*.media_id' => 'required|integer|exists:media,id',
            'media.*.position' => 'nullable|integer|min:0',
            'media.*.caption' => 'nullable|string|max:255',
            'media.*.alt_text' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($event, $data) {
            $incomingIds = [];
            $existingIds = $event->gallery()->pluck('media_id')->all();

            foreach (array_values($data['media']) as $position => $item) {
                $mediaId = (int) $item['media_id'];
                $incomingIds[] = $mediaId;

                EventGallery::updateOrCreate(
                    [
                        'event_id' => $event->id,
                        'media_id' => $mediaId,
                    ],
                    [
                        'position' => (int) ($item['position'] ?? $position),
                        'caption' => $item['caption'] ?? null,
                    ],
                );
            }

            $removedIds = array_values(array_diff($existingIds, $incomingIds));

            if (! empty($removedIds)) {
                EventGallery::query()
                    ->where('event_id', $event->id)
                    ->whereIn('media_id', $removedIds)
                    ->delete();

                Media::whereIn('id', $removedIds)->decrement('usage_count');
            }

            $addedIds = array_values(array_diff($incomingIds, $existingIds));

            if (! empty($addedIds)) {
                Media::whereIn('id', $addedIds)->increment('usage_count');
            }
        });

        return response()->json([
            'status' => 'ok',
            'gallery' => $event->fresh(['gallery.media'])->gallery->map(function (EventGallery $item) {
                return [
                    'id' => $item->id,
                    'media_id' => $item->media_id,
                    'position' => $item->position,
                    'caption' => $item->caption,
                    'media' => $item->media ? [
                        'id' => $item->media->id,
                        'filename' => $item->media->filename,
                        'path' => $item->media->path,
                    ] : null,
                ];
            })->values(),
        ]);
    }

    public function restore($id)
    {
        $event = Event::withTrashed()->findOrFail($id);

        $this->authorize('restore', $event);

        if ($event->trashed()) {
            $event->restore();
        }

        return redirect()->route('events.index')->with('success', 'Event restored.');
    }

    private function makeUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $index = 1;

        while (
            Event::where('slug', $slug)
            ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $base . '-' . $index++;
        }

        return $slug;
    }
}
