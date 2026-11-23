<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Contracts\View\View;

class DistrictController extends Controller
{
    public function index(): View
    {
        $districts = District::active()
            ->with(['thumbnail'])
            ->orderBy('position')
            ->get(['id', 'name', 'slug', 'svg_path_id', 'short_summary', 'color', 'icon', 'position', 'thumbnail_id']);

        return view('districts.index', [
            'title'     => 'Distritos Municipales',
            'districts' => $districts,
        ]);
    }

    public function show(string $slug): View
    {
        $district = District::active()
            ->with(['heroImage', 'mapImage', 'thumbnail'])
            ->where('slug', $slug)
            ->firstOrFail();

        $related = District::active()
            ->where('id', '!=', $district->id)
            ->orderBy('position')
            ->get(['id', 'name', 'slug', 'short_summary', 'color', 'icon', 'position', 'thumbnail_id'])
            ->take(4);

        return view('districts.show', [
            'title'    => $district->name . ' — Alcaldía Municipal de La Unión',
            'district' => $district,
            'related'  => $related,
        ]);
    }
}
