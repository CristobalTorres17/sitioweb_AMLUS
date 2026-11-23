<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DistrictController extends Controller
{
    public function index()
    {
        $districts = District::withTrashed()
            ->orderBy('position')
            ->get(['id', 'name', 'slug', 'svg_path_id', 'color', 'position', 'is_active', 'deleted_at']);

        return Inertia::render('Districts/Index', ['districts' => $districts]);
    }

    public function create()
    {
        return Inertia::render('Districts/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:districts,slug',
            'svg_path_id'      => 'nullable|string|max:100',
            'short_summary'    => 'nullable|string|max:500',
            'description'      => 'nullable|string|max:1000',
            'content'          => 'nullable|string',
            'color'            => 'nullable|string|max:20',
            'icon'             => 'nullable|string|max:100',
            'hero_image_id'    => 'nullable|integer|exists:media,id',
            'map_image_id'     => 'nullable|integer|exists:media,id',
            'thumbnail_id'     => 'nullable|integer|exists:media,id',
            'position'         => 'nullable|integer|min:0',
            'is_active'        => 'boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
        ]);

        if (!empty($data['content'])) {
            $data['content'] = app('purifier')->clean($data['content']);
        }

        $slug = $data['slug'] ?? Str::slug($data['name']);
        $base = $slug;
        $i = 1;
        while (District::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        District::create([
            'name'          => $data['name'],
            'slug'          => $slug,
            'svg_path_id'   => $data['svg_path_id'] ?? null,
            'short_summary' => $data['short_summary'] ?? null,
            'description'   => $data['description'] ?? null,
            'color'         => $data['color'] ?? '#ffcc00',
            'icon'          => $data['icon'] ?? null,
            'hero_image_id' => $data['hero_image_id'] ?? null,
            'map_image_id'  => $data['map_image_id'] ?? null,
            'thumbnail_id'  => $data['thumbnail_id'] ?? null,
            'position'      => $data['position'] ?? (District::max('position') + 1),
            'is_active'     => (bool) ($data['is_active'] ?? true),
        ]);

        return redirect()->route('districts.index')->with('success', 'Distrito creado.');
    }

    public function edit(District $district)
    {
        $district->load(['heroImage', 'mapImage', 'thumbnail']);

        return Inertia::render('Districts/Edit', ['district' => $district]);
    }

    public function update(Request $request, District $district)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:districts,slug,' . $district->id,
            'svg_path_id'      => 'nullable|string|max:100',
            'short_summary'    => 'nullable|string|max:500',
            'description'      => 'nullable|string|max:1000',
            'content'          => 'nullable|string',
            'color'            => 'nullable|string|max:20',
            'icon'             => 'nullable|string|max:100',
            'hero_image_id'    => 'nullable|integer|exists:media,id',
            'map_image_id'     => 'nullable|integer|exists:media,id',
            'thumbnail_id'     => 'nullable|integer|exists:media,id',
            'position'         => 'nullable|integer|min:0',
            'is_active'        => 'boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
        ]);

        if (!empty($data['content'])) {
            $data['content'] = app('purifier')->clean($data['content']);
        }

        $slug = $data['slug'] ?? Str::slug($data['name']);
        $base = $slug;
        $i = 1;
        while (District::where('slug', $slug)->where('id', '!=', $district->id)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $district->update(array_merge($data, ['slug' => $slug]));

        return redirect()->route('districts.index')->with('success', 'Distrito actualizado.');
    }

    public function destroy(District $district)
    {
        $district->delete();
        return redirect()->route('districts.index')->with('success', 'Distrito eliminado.');
    }

    public function moveUp(District $district)
    {
        $prev = District::where('position', '<', $district->position)->orderBy('position', 'desc')->first();
        if ($prev) {
            [$prev->position, $district->position] = [$district->position, $prev->position];
            $prev->save();
            $district->save();
        }
        return back();
    }

    public function moveDown(District $district)
    {
        $next = District::where('position', '>', $district->position)->orderBy('position')->first();
        if ($next) {
            [$next->position, $district->position] = [$district->position, $next->position];
            $next->save();
            $district->save();
        }
        return back();
    }
}
