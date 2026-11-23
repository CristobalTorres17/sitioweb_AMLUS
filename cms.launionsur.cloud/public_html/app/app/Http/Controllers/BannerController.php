<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\District;
use Illuminate\Http\Request;
use App\Http\Requests\BannerStoreRequest;
use App\Http\Requests\BannerUpdateRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class BannerController extends Controller
{
    public function index()
    {
        abort_unless(auth()->check(), 403);
        $banners = Banner::with('media')->orderBy('page')->orderBy('sort_order')->get();

        return Inertia::render('Banners/Index', [
            'banners' => $banners,
        ]);
    }

    public function create()
    {
        abort_unless(auth()->check(), 403);
        $pages = $this->buildPageOptions();

        return Inertia::render('Banners/Create', [
            'pages' => $pages,
        ]);
    }

    public function store(BannerStoreRequest $request)
    {
        abort_unless(auth()->check(), 403);
        $data = $request->validated();

        Log::debug('BannerController.store called', [
            'has_file' => $request->hasFile('image'),
            'request_all' => $request->all(),
            'validated' => $data,
        ]);

        // Validate: Maximum 10 banners per page
        $activeCount = Banner::where('page', $data['page'])
            ->where('is_active', true)
            ->count();

        if ($activeCount >= 10) {
            return back()
                ->withErrors(['page' => 'Maximum 10 banners per page'])
                ->withInput();
        }

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('banners', 'public');
        } elseif (!empty($data['image_path'])) {
            $path = $data['image_path'];
        }

        Banner::create([
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'image_path' => $path,
            'cta_text' => $data['cta_text'] ?? null,
            'cta_url' => $data['cta_url'] ?? null,
            'show_button' => (bool) ($data['show_button'] ?? true),
            'page' => $data['page'],
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return redirect()->route('banners.index')->with('success', 'Banner created');
    }

    public function edit(Banner $banner)
    {
        abort_unless(auth()->check(), 403);
        $pages = $this->buildPageOptions();

        $banner->load('media');

        return Inertia::render('Banners/Edit', [
            'banner' => $banner,
            'pages' => $pages,
            'previewUrl' => $banner->image_path ? Storage::disk('public')->url($banner->image_path) : null,
        ]);
    }

    public function update(BannerUpdateRequest $request, Banner $banner)
    {
        // Debug: replaced dd with log so request flow is not interrupted
        Log::debug('BannerController.update called (entry)', [
            'banner_id' => $banner->id,
            'request_all' => $request->all(),
        ]);
        abort_unless(auth()->check(), 403);
        $data = $request->validated();

        Log::debug('BannerController.update called', [
            'has_file' => $request->hasFile('image'),
            'request_all' => $request->all(),
            'validated' => $data,
            'banner_id' => $banner->id,
        ]);

        // Validate: Maximum 10 banners per page (excluding current banner)
        if ($data['is_active'] ?? $banner->is_active) {
            $activeCount = Banner::where('page', $data['page'])
                ->where('is_active', true)
                ->where('id', '!=', $banner->id)
                ->count();

            if ($activeCount >= 10) {
                return back()
                    ->withErrors(['page' => 'Maximum 10 banners per page'])
                    ->withInput();
            }
        }

        if ($request->hasFile('image')) {
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }

            $banner->image_path = $request->file('image')->store('banners', 'public');
        } elseif (!empty($data['image_path'])) {
            $banner->image_path = $data['image_path'];
        }

        $banner->fill([
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'cta_text' => $data['cta_text'] ?? null,
            'cta_url' => $data['cta_url'] ?? null,
            'show_button' => (bool) ($data['show_button'] ?? true),
            'page' => $data['page'],
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        $banner->save();

        return redirect()->route('banners.index')->with('success', 'Banner updated');
    }

    public function destroy(Banner $banner)
    {
        abort_unless(auth()->check(), 403);
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $banner->delete();

        return redirect()->route('banners.index')->with('success', 'Banner deleted');
    }

    public function moveUp(Banner $banner)
    {
        abort_unless(auth()->check(), 403);
        $previous = Banner::where('page', $banner->page)
            ->where('sort_order', '<', $banner->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        if ($previous) {
            $currentOrder = $banner->sort_order;
            $banner->sort_order = $previous->sort_order;
            $previous->sort_order = $currentOrder;

            $banner->save();
            $previous->save();
        }

        return back()->with('success', 'Banner order updated');
    }

    public function moveDown(Banner $banner)
    {
        abort_unless(auth()->check(), 403);
        $next = Banner::where('page', $banner->page)
            ->where('sort_order', '>', $banner->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($next) {
            $currentOrder = $banner->sort_order;
            $banner->sort_order = $next->sort_order;
            $next->sort_order = $currentOrder;

            $banner->save();
            $next->save();
        }

        return back()->with('success', 'Banner order updated');
    }

    private function buildPageOptions(): array
    {
        $pages = config('cms.banner_pages', []);
        $districtPages = District::where('is_active', true)
            ->orderBy('position')
            ->get(['name', 'slug'])
            ->mapWithKeys(fn($d) => ['district:' . $d->slug => 'Distrito: ' . $d->name])
            ->toArray();

        return array_merge($pages, $districtPages);
    }
}
