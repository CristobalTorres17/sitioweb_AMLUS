<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Event;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $events = Event::published()
            ->with('featuredImage')
            ->latest('event_date')
            ->limit(3)
            ->get(['id', 'title', 'slug', 'event_date', 'description', 'featured_image_id', 'metadata']);

        $banners = Banner::active()
            ->with('media')
            ->orderBy('sort_order')
            ->get(['id', 'title', 'subtitle', 'page', 'sort_order', 'show_button', 'image_path']);
        //dd($banners->toArray());
        // Services (kind='service')
        $services = Service::active()
            ->where('kind', 'service')
            ->with([
                'category' => function ($query) {
                    $query->withoutGlobalScope('service_type');
                },
            ])
            ->orderBy('order')
            ->limit(4)
            ->get(['id', 'category_id', 'title', 'slug', 'description', 'estimated_time', 'order']);

        $featuredServices = Service::active()
            ->where('kind', 'service')
            ->where('is_featured', true)
            ->with([
                'category' => function ($query) {
                    $query->withoutGlobalScope('service_type');
                },
            ])
            ->orderBy('featured_order')
            ->limit(4)
            ->get(['id', 'category_id', 'title', 'slug', 'description', 'estimated_time', 'is_featured', 'featured_order']);

        // Tramites (kind='procedure')
        $tramites = Service::active()
            ->where('kind', 'procedure')
            ->with([
                'category' => function ($query) {
                    $query->withoutGlobalScope('service_type');
                },
            ])
            ->orderBy('order')
            ->limit(4)
            ->get(['id', 'category_id', 'title', 'slug', 'description', 'estimated_time', 'order']);

        $featuredTramites = Service::active()
            ->where('kind', 'procedure')
            ->where('is_featured', true)
            ->with([
                'category' => function ($query) {
                    $query->withoutGlobalScope('service_type');
                },
            ])
            ->orderBy('featured_order')
            ->limit(4)
            ->get(['id', 'category_id', 'title', 'slug', 'description', 'estimated_time', 'is_featured', 'featured_order']);

        // Normalize lists (use fallback lists when no explicit featured items exist)
        $svcList = $featuredServices->isNotEmpty() ? $featuredServices : $services;
        $trmList = $featuredTramites->isNotEmpty() ? $featuredTramites : $tramites;

        // Map to a unified structure for the slider
        $svcMapped = $svcList->map(function ($s) {
            return [
                'id' => $s->id,
                'title' => $s->title,
                'slug' => $s->slug,
                'description' => $s->description,
                'icon' => data_get($s, 'icon'),
                'category_name' => data_get($s, 'category.name'),
                'type' => 'service',
                'featured_order' => data_get($s, 'featured_order') ?? data_get($s, 'order') ?? 9999,
            ];
        });

        $trmMapped = $trmList->map(function ($t) {
            return [
                'id' => $t->id,
                'title' => $t->title,
                'slug' => $t->slug,
                'description' => $t->description,
                'icon' => data_get($t, 'icon'),
                'category_name' => data_get($t, 'category.name'),
                'type' => 'tramite',
                'featured_order' => data_get($t, 'featured_order') ?? data_get($t, 'order') ?? 9999,
            ];
        });

        $featuredItems = $svcMapped->concat($trmMapped)
            ->sortBy('featured_order')
            ->values();

        return view('home.index', [
            'events' => $events,
            'banners' => $banners,
            'services' => $services,
            'featuredServices' => $svcList,
            'tramites' => $tramites,
            'featuredTramites' => $trmList,
            'featuredItems' => $featuredItems,
        ]);
    }
}
