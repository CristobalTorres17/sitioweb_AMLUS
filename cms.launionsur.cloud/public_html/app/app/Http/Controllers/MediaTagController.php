<?php

namespace App\Http\Controllers;

use App\Models\MediaTag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MediaTagController extends Controller
{
    /**
     * Display list of all tags
     */
    public function index(Request $request): Response
    {
        $search = $request->get('search', '');

        $query = MediaTag::withCount('media');

        // Search by name
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $tags = $query->orderBy('name')->paginate(20);

        return Inertia::render('Media/Tags', [
            'tags' => $tags,
            'search' => $search,
        ]);
    }

    /**
     * Show create form
     */
    public function create(): Response
    {
        // Pre-defined color palette for quick selection
        $colorPalette = [
            '#ef4444', // red
            '#f97316', // orange
            '#eab308', // yellow
            '#22c55e', // green
            '#10b981', // emerald
            '#06b6d4', // cyan
            '#0ea5e9', // sky
            '#3b82f6', // blue
            '#6366f1', // indigo
            '#8b5cf6', // violet
            '#d946ef', // fuchsia
            '#ec4899', // pink
        ];

        return Inertia::render('Media/TagCreate', [
            'colorPalette' => $colorPalette,
        ]);
    }

    /**
     * Store new tag
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:media_tags,name|max:50',
            'slug' => 'sometimes|string|unique:media_tags,slug|max:50',
            'description' => 'nullable|string|max:255',
            'color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        try {
            // Generate slug if not provided
            if (empty($validated['slug'])) {
                $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
            }

            $validated['created_by'] = auth()->id();

            MediaTag::create($validated);

            return redirect()
                ->route('media-tags.index')
                ->with('success', 'Tag created successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to create tag: ' . $e->getMessage());
        }
    }

    /**
     * Show edit form
     */
    public function edit(MediaTag $mediaTag): Response
    {
        // Pre-defined color palette
        $colorPalette = [
            '#ef4444',
            '#f97316',
            '#eab308',
            '#22c55e',
            '#10b981',
            '#06b6d4',
            '#0ea5e9',
            '#3b82f6',
            '#6366f1',
            '#8b5cf6',
            '#d946ef',
            '#ec4899',
        ];

        $mediaTag->load('media:id');

        return Inertia::render('Media/TagEdit', [
            'tag' => [
                'id' => $mediaTag->id,
                'name' => $mediaTag->name,
                'slug' => $mediaTag->slug,
                'description' => $mediaTag->description,
                'color' => $mediaTag->color,
                'media_count' => $mediaTag->media()->count(),
            ],
            'colorPalette' => $colorPalette,
        ]);
    }

    /**
     * Update tag
     */
    public function update(Request $request, MediaTag $mediaTag): RedirectResponse
    {
        $validated = $request->validate([
            'name' => "required|string|unique:media_tags,name,{$mediaTag->id}|max:50",
            'slug' => "sometimes|string|unique:media_tags,slug,{$mediaTag->id}|max:50",
            'description' => 'nullable|string|max:255',
            'color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        try {
            // Generate slug if not provided
            if (empty($validated['slug'])) {
                $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
            }

            $mediaTag->update($validated);

            return redirect()
                ->route('media-tags.index')
                ->with('success', 'Tag updated successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update tag: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete tag
     */
    public function destroy(MediaTag $mediaTag): RedirectResponse
    {
        try {
            $mediaTag->delete();

            return redirect()
                ->route('media-tags.index')
                ->with('success', 'Tag deleted successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete tag: ' . $e->getMessage());
        }
    }
}
