<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TramiteController extends Controller
{
    public function index(Request $request): View
    {
        $categories = ServiceCategory::withoutGlobalScope('service_type')
            ->active()
            ->whereHas('services', function ($query) {
                $query->active()->where('kind', ServiceCategory::TYPE_PROCEDURE);
            })
            ->with([
                'services' => function ($query) {
                    $query->active()
                        ->where('kind', ServiceCategory::TYPE_PROCEDURE)
                        ->orderBy('order')
                        ->select('id', 'category_id', 'title', 'slug', 'description', 'estimated_time', 'order');
                },
            ])
            ->orderBy('position')
            ->get(['id', 'name', 'slug', 'description', 'position']);

        $servicesQuery = Service::active()
            ->where('kind', ServiceCategory::TYPE_PROCEDURE)
            ->with([
                'category' => function ($query) {
                    $query->withoutGlobalScope('service_type');
                },
            ]);

        if ($request->filled('categoria')) {
            $servicesQuery->whereHas('category', function ($categoryQuery) use ($request) {
                $categoryQuery
                    ->withoutGlobalScope('service_type')
                    ->where('slug', $request->input('categoria'));
            });
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $servicesQuery->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        $services = $servicesQuery
            ->orderBy('order')
            ->get(['id', 'category_id', 'title', 'slug', 'description', 'estimated_time', 'order']);

        return view('tramites.index', [
            'title' => 'Trámites',
            'categories' => $categories,
            'services' => $services,
        ]);
    }

    public function show(string $slug): View
    {
        $service = Service::active()
            ->where('kind', ServiceCategory::TYPE_PROCEDURE)
            ->with([
                'category' => function ($query) {
                    $query->withoutGlobalScope('service_type');
                },
                'steps' => function ($query) {
                    $query->orderBy('sort_order');
                },
                'requirements' => function ($query) {
                    $query->orderBy('sort_order');
                },
                'media',
            ])
            ->where('slug', $slug)
            ->firstOrFail([
                'id',
                'category_id',
                'title',
                'slug',
                'description',
                'estimated_time',
                'order',
                'cost',
                'schedule',
                'documentation',
                'icon',
            ]);

        $relatedItemsQuery = Service::active()
            ->where('kind', ServiceCategory::TYPE_PROCEDURE)
            ->where('services.id', '!=', $service->id);

        if ($service->category_id) {
            $relatedItemsQuery->where('category_id', $service->category_id);
        }

        $relatedItems = $relatedItemsQuery
            ->orderBy('order')
            ->limit(5)
            ->get(['id', 'category_id', 'title', 'slug', 'description', 'estimated_time', 'order']);


        return view('tramites.show', [
            'title' => $service->title,
            'service' => $service,
            'relatedItems' => $relatedItems,
        ]);
    }
}
