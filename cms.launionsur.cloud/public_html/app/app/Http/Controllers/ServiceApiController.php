<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceApiController extends Controller
{
    public function categories()
    {
        $categories = ServiceCategory::where('is_active', true)
            ->with(['services' => function ($q) {
                $q->where('is_active', true)->orderBy('order');
            }])
            ->orderBy('position')
            ->get();

        return response()->json(['data' => $categories]);
    }

    public function category($slug)
    {
        $category = ServiceCategory::where('slug', $slug)
            ->where('is_active', true)
            ->with(['services' => function ($q) {
                $q->where('is_active', true)->orderBy('order');
            }])
            ->firstOrFail();

        return response()->json(['data' => $category]);
    }

    public function show($slug)
    {
        $service = Service::where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'steps' => function ($q) {
                    $q->orderBy('sort_order');
                },
                'requirements' => function ($q) {
                    $q->orderBy('sort_order');
                },
            ])
            ->firstOrFail();

        return response()->json(['data' => $service]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $limit = (int) $request->query('limit', 20);

        if ($q === '') {
            return response()->json(['data' => []]);
        }

        // Use FULLTEXT search for title and description. Fall back to LIKE if DB doesn't support MATCH.
        $query = Service::where('is_active', true)
            ->with([
                'steps' => function ($q) {
                    $q->orderBy('sort_order');
                },
                'requirements' => function ($q) {
                    $q->orderBy('sort_order');
                },
            ])
            ->limit($limit);

        // Use raw MATCH ... AGAINST for fulltext search on MySQL/MariaDB; fallback to LIKE for other drivers (sqlite tests)
        $driver = \Illuminate\Support\Facades\Schema::getConnection()->getDriverName();
        if (in_array($driver, ['mysql', 'mysqli'])) {
            $query->whereRaw('MATCH(title, description) AGAINST(? IN NATURAL LANGUAGE MODE)', [$q]);
        } else {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        $results = $query->get();

        return response()->json(['data' => $results]);
    }
}
