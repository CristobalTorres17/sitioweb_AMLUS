<?php

namespace App\Services;

use App\Models\Page;
use App\Models\CmsBlock;
use App\Support\MediaUrlResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Throwable;

class CmsBlockDisplayService
{
    public function resolvePageSlug(?string $routeName): ?string
    {
        if (!$routeName) {
            return config('cms.pages.home', 'home');
        }

        $pages = config('cms.pages', []);

        return $pages[$routeName] ?? config('cms.pages.home', 'home');
    }

    public function blocksForPage(string $pageSlug): Collection
    {
        $blocks = CmsBlock::query()
            ->active()
            ->forPage($pageSlug)
            ->with('media')
            ->get();

        return $blocks->each(function ($block) {
            $block->content = $this->rewriteContentUrls((string) ($block->content ?? ''));
        });
    }

    /**
     * Rewrite /storage/media/ paths in pre-rendered block HTML to use the configured
     * CMS media URL so the website can consume assets from the CMS subdomain or a
     * shared storage backend.
     */
    private function rewriteContentUrls(string $content): string
    {
        if (!$content) {
            return $content;
        }

        $cmsMediaBase = rtrim(
            config('filesystems.disks.cms_public.url', url('/cms-media')),
            '/'
        );

        // Replace non-original media references with the resolved CMS original URL.
        return preg_replace_callback(
            '#/storage/media/(?!original/)([^"\'\)\s>]+)#',
            function (array $matches) use ($cmsMediaBase): string {
                $relativePath = 'media/original/' . ltrim($matches[1], '/');

                return MediaUrlResolver::resolve($relativePath, $cmsMediaBase . '/' . $relativePath) ?? $matches[0];
            },
            $content
        ) ?? $content;
    }

    public function blocksForPageZone(string $pageSlug, string $zone): Collection
    {
        return CmsBlock::query()
            ->active()
            ->forPageZone($pageSlug, $zone)
            ->with('media')
            ->get()
            ->each(function ($block) {
                $block->content = $this->rewriteContentUrls((string) ($block->content ?? ''));
            });
    }

    public function footerBlocks(): Collection
    {
        return CmsBlock::query()
            ->active()
            ->forZone('footer')
            ->with('media')
            ->orderBy('id')
            ->get()
            ->each(function ($block) {
                $block->content = $this->rewriteContentUrls((string) ($block->content ?? ''));
            });
    }

    public function navigationPages(?string $currentPageSlug = null): Collection
    {
        return $this->navigationPageData()
            ->map(function (array $page) use ($currentPageSlug): array {
                return [
                    'title' => $page['title'],
                    'slug' => $page['slug'],
                    'href' => $this->resolveNavigationHref($page['slug']),
                    'description' => $page['description'] ?? null,
                    'active' => $page['slug'] === $currentPageSlug,
                ];
            })
            ->values();
    }

    private function navigationPageData(): Collection
    {
        $cacheKey = 'website.navigation.pages.raw.v2';

        $pages = Cache::remember($cacheKey, now()->addMinutes(10), function (): array {
            return $this->fetchNavigationPageData()->all();
        });

        return collect($pages);
    }

    private function fetchNavigationPageData(): Collection
    {
        try {
            $excludedSlugs = $this->navigationExcludedSlugs();

            return Page::query()
                ->published()
                ->orderBy('title')
                ->get([
                    'id',
                    'title',
                    'slug',
                    'description',
                    'published_at',
                ])
                ->reject(function (Page $page) use ($excludedSlugs): bool {
                    return in_array($page->slug, $excludedSlugs, true);
                })
                ->map(function (Page $page): array {
                    return [
                        'title' => trim((string) $page->title),
                        'slug' => trim((string) $page->slug),
                        'description' => isset($page->description) ? trim((string) $page->description) : null,
                    ];
                })
                ->values();
        } catch (Throwable) {
            return collect();
        }
    }

    private function navigationExcludedSlugs(): array
    {
        return array_values(array_unique(array_filter([
            $this->resolvePageSlug('home'),
            $this->resolvePageSlug('services.index'),
            $this->resolvePageSlug('tramites.index'),
            $this->resolvePageSlug('districts.index'),
            $this->resolvePageSlug('events.index'),
            $this->resolvePageSlug('contact.show'),
        ])));
    }

    private function resolveNavigationHref(string $slug): string
    {
        $routeName = $this->navigationRouteNameForSlug($slug);

        if ($routeName && Route::has($routeName)) {
            return route($routeName);
        }

        if (Route::has('pages.show')) {
            return route('pages.show', ['slug' => $slug]);
        }

        return url('/paginas/' . rawurlencode($slug));
    }

    private function navigationRouteNameForSlug(string $slug): ?string
    {
        foreach (config('cms.pages', []) as $routeName => $mappedSlug) {
            if ($mappedSlug === $slug) {
                return $routeName;
            }
        }

        return null;
    }

    public function sharedViewData(Request $request): array
    {
        $routeName = optional($request->route())->getName();
        $pageSlug = $this->resolvePageSlug($routeName);

        if ($routeName === 'pages.show') {
            $dynamicSlug = $request->route('slug');
            if (is_string($dynamicSlug) && $dynamicSlug !== '') {
                $pageSlug = $dynamicSlug;
            }
        }
        $explicitEntitySlug = $request->attributes->get('cmsEntityPageSlug');

        // For district and event detail pages, also load blocks scoped to the specific entity.
        // Blocks for 'district:{slug}' / 'event:{slug}' are merged on top of the generic page blocks.
        $specificEntitySlug = null;
        if (is_string($explicitEntitySlug) && $explicitEntitySlug !== '') {
            $specificEntitySlug = $explicitEntitySlug;
        } elseif ($routeName === 'districts.show') {
            $districtSlug = $request->route('district');
            if ($districtSlug) {
                $specificEntitySlug = 'district:' . $districtSlug;
            }
        } elseif ($routeName === 'events.show') {
            $eventSlug = $request->route('slug');
            if ($eventSlug) {
                $specificEntitySlug = 'event:' . $eventSlug;
            }
        }

        $pageBlocks = $pageSlug ? $this->blocksForPage($pageSlug)->groupBy('zone') : collect();

        if ($specificEntitySlug) {
            $specificBlocks = $this->blocksForPage($specificEntitySlug)->groupBy('zone');

            // Merge: entity-specific blocks are appended after the generic ones per zone.
            $pageBlocks = $pageBlocks->map(function ($zoneBlocks, $zone) use ($specificBlocks) {
                $extra = $specificBlocks->get($zone, collect());
                return $zoneBlocks->concat($extra);
            });

            // Add zones that only exist in the specific slug
            foreach ($specificBlocks as $zone => $blocks) {
                if (!$pageBlocks->has($zone)) {
                    $pageBlocks->put($zone, $blocks);
                }
            }
        }

        return [
            'cmsCurrentPageSlug' => $pageSlug,
            'cmsPageBlocksByZone' => $pageBlocks,
            'cmsFooterBlocks' => $this->footerBlocks(),
            'cmsNavigationPages' => $this->navigationPages($pageSlug),
        ];
    }
}
