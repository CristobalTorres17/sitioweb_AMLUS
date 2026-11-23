<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\MetaTagService;
use Inertia\Inertia;

class PublicPageController extends Controller
{
    /**
     * Display a published page
     */
    public function show(Page $page)
    {
        // Only show published pages to public
        if (!$page->is_published) {
            abort(404);
        }

        // Get the full URL for meta tags
        $url = request()->url();

        // Generate meta tags
        $metaTags = MetaTagService::generateMetaTags($page, $url);
        $pageMeta = MetaTagService::getPageMeta($page, $url);

        // Prepare breadcrumbs for component
        $breadcrumbs = [
            [
                'label' => config('app.name', 'Home'),
                'href' => '/',
                'current' => false,
            ],
            [
                'label' => 'Páginas',
                'href' => url('/pages'),
                'current' => false,
            ],
            [
                'label' => $page->title,
                'href' => null,
                'current' => true,
            ],
        ];

        return Inertia::render('Pages/Show', [
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'content' => $page->content,
                'published_at' => $page->published_at?->toDateTimeString(),
                'creator' => $page->creator ? [
                    'id' => $page->creator->id,
                    'name' => $page->creator->name,
                ] : null,
            ],
            'breadcrumbs' => $breadcrumbs,
            'meta' => [
                'canonical' => $metaTags['canonical'],
                'title' => $metaTags['title'],
                'description' => $metaTags['description'],
                'keywords' => $metaTags['keywords'],
                'og_title' => $metaTags['og_title'],
                'og_description' => $metaTags['og_description'],
                'og_image' => $metaTags['og_image'],
                'og_url' => $metaTags['og_url'],
                'og_type' => $metaTags['og_type'],
                'twitter_title' => $metaTags['twitter_title'] ?? null,
                'twitter_description' => $metaTags['twitter_description'] ?? null,
                'twitter_card' => $metaTags['twitter_card'],
                'twitter_image' => $metaTags['twitter_image'],
                'breadcrumb_schema' => $metaTags['breadcrumb_schema'],
            ],
        ]);
    }

    /**
     * List all published pages
     */
    public function index()
    {
        $pages = Page::where('is_published', true)
            ->with('creator')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return Inertia::render('Pages/PublicIndex', [
            'pages' => $pages->through(function ($page) {
                return [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'description' => $page->description,
                    'published_at' => $page->published_at?->toDateTimeString(),
                    'creator' => $page->creator ? [
                        'id' => $page->creator->id,
                        'name' => $page->creator->name,
                    ] : null,
                ];
            }),
        ]);
    }
}
