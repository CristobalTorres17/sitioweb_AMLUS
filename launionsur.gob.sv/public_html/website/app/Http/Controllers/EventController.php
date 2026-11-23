<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $searchTerm = $request->input('q');
        $activeCategory = $request->input('categoria');

        // Get events
        $eventsQuery = Event::published()
            ->with('featuredImage');

        $categoryExpression = Event::categoryExpression();

        if ($activeCategory) {
            $eventsQuery->whereRaw($categoryExpression . ' = ?', [Str::lower($activeCategory)]);
        }

        if ($searchTerm) {
            $eventsQuery->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        $events = $eventsQuery
            ->select(['id', 'title', 'slug', 'event_date', 'description', 'featured_image_id', 'metadata'])
            ->get()
            ->map(fn($event) => (object) [
                'id' => $event->id,
                'title' => $event->title,
                'slug' => $event->slug,
                'description' => $event->description,
                'date' => $event->event_date,
                'featured_image_id' => $event->featured_image_id,
                'featuredImage' => $event->featuredImage,
                'metadata' => $event->metadata,
                'type' => 'event',
            ]);

        // Get articles
        $articlesQuery = Article::published()
            ->with('media');

        if ($activeCategory) {
            $articlesQuery->whereHas('category', fn($q) => $q->where('slug', $activeCategory));
        }

        if ($searchTerm) {
            $articlesQuery->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('excerpt', 'like', '%' . $searchTerm . '%')
                    ->orWhere('content', 'like', '%' . $searchTerm . '%');
            });
        }

        $articles = $articlesQuery
            ->select(['id', 'title', 'slug', 'excerpt', 'content', 'published_at', 'category_id'])
            ->get()
            ->map(fn($article) => (object) [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'description' => $article->excerpt ?: strip_tags($article->content),
                'date' => $article->published_at,
                'featured_image_id' => null,
                'featuredImage' => $article->media->first(),
                'metadata' => [],
                'type' => 'article',
            ]);

        // Merge and sort by date (most recent first)
        $combined = $events->concat($articles)
            ->sortByDesc(fn($item) => $item->date)
            ->values();

        // Paginate the merged feed after sorting events and articles together.
        $page = $request->query('page', 1);
        $perPage = 9;
        $total = $combined->count();
        $items = $combined->forPage($page, $perPage)->values();

        $events = new LengthAwarePaginator(
            $items->all(),
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // Get categories
        $categories = Category::withoutGlobalScope('article_type')
            ->where('type', Category::TYPE_EVENT)
            ->orderBy('position')
            ->get(['id', 'name', 'slug', 'color', 'icon'])
            ->map(fn($cat) => (object) [
                'slug' => $cat->slug,
                'name' => $cat->name,
            ]);

        if ($categories->isEmpty()) {
            $categories = Event::published()
                ->selectRaw($categoryExpression . ' as slug')
                ->whereRaw($categoryExpression . ' is not null and ' . $categoryExpression . " <> ''")
                ->distinct()
                ->orderBy('slug')
                ->pluck('slug')
                ->map(fn($slug) => (object) [
                    'slug' => $slug,
                    'name' => Str::headline($slug),
                ]);
        }

        if ($categories->isEmpty()) {
            $categories = collect([
                (object) ['name' => 'Cultura', 'slug' => 'cultura'],
                (object) ['name' => 'Obras', 'slug' => 'obras'],
                (object) ['name' => 'Salud', 'slug' => 'salud'],
                (object) ['name' => 'Educación', 'slug' => 'educacion'],
            ]);
        }
        return view('events.index', [
            'title' => 'Noticias',
            'events' => $events,
            'categories' => $categories,
        ]);
    }

    public function show(string $slug): View
    {
        // Try to find as event first
        $event = Event::published()
            ->with('featuredImage')
            ->where('slug', $slug)
            ->first(['id', 'title', 'slug', 'description', 'content', 'event_date', 'location', 'organizer', 'featured_image_id', 'metadata', 'is_featured', 'published_at']);

        $isArticle = false;

        // If not found, try as article
        if (!$event) {
            $article = Article::published()
                ->with('media')
                ->where('slug', $slug)
                ->first(['id', 'title', 'slug', 'excerpt', 'content', 'published_at', 'is_featured']);

            if (!$article) {
                abort(404);
            }

            $event = $article;
            $isArticle = true;
        }

        // Let CmsBlockDisplayService know the exact entity page slug for this detail.
        request()->attributes->set('cmsEntityPageSlug', $isArticle ? 'article:' . $slug : 'event:' . $slug);

        $wordCount = str_word_count(strip_tags($event->content ?? ''));
        $readingTime = max(1, (int) ceil($wordCount / 200));

        // Get related content (both events and articles)
        if ($isArticle) {
            $relatedEventsQuery = Article::published()
                ->with('media')
                ->where('id', '!=', $event->id);
        } else {
            $relatedEventsQuery = Event::published()
                ->with('featuredImage')
                ->where('id', '!=', $event->id);

            $categoryExpression = Event::categoryExpression();
            $legacyCategorySlug = data_get($event, 'metadata.legacy_event_type')
                ?: data_get($event, 'metadata.category')
                ?: data_get($event, 'metadata.tipo');

            if ($legacyCategorySlug) {
                $relatedEventsQuery->whereRaw($categoryExpression . ' = ?', [Str::lower($legacyCategorySlug)]);
            }
        }

        $relatedEvents = $relatedEventsQuery
            ->latest($isArticle ? 'published_at' : 'event_date')
            ->take(3)
            ->get();

        // If empty, get a mix of both events and articles
        if ($relatedEvents->isEmpty()) {
            $events = Event::published()
                ->with('featuredImage')
                ->where('id', '!=', $event->id ?? null)
                ->latest('event_date')
                ->take(2)
                ->get(['id', 'title', 'slug', 'description', 'event_date', 'featured_image_id', 'metadata']);

            $articles = Article::published()
                ->with('media')
                ->where('id', '!=', $event->id ?? null)
                ->latest('published_at')
                ->take(1)
                ->get(['id', 'title', 'slug', 'excerpt', 'published_at']);

            $relatedEvents = $events->concat($articles)->take(3);
        }

        return view('events.show', [
            'title' => $event->title,
            'event' => $event,
            'readingTime' => $readingTime,
            'relatedEvents' => $relatedEvents,
            'isArticle' => $isArticle,
        ]);
    }
}
