<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageVersion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageVersionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the version history for a page.
     */
    public function index(Page $page)
    {
        $versions = $page->versions()
            ->with('creator')
            ->orderBy('version_number', 'desc')
            ->paginate(10)
            ->through(function ($version) {
                return [
                    'id' => $version->id,
                    'version_number' => $version->version_number,
                    'title' => $version->title,
                    'was_published' => (bool) $version->was_published,
                    'change_reason' => $version->change_reason,
                    'restored_from' => $version->restored_from,
                    'created_at' => $version->created_at->toDateTimeString(),
                    'creator' => $version->creator ? [
                        'id' => $version->creator->id,
                        'name' => $version->creator->name,
                    ] : null,
                ];
            });

        return Inertia::render('Pages/VersionHistory', [
            'page_id' => $page->id,
            'versions' => $versions,
        ]);
    }

    /**
     * Show a specific version of a page.
     */
    public function show(Page $page, PageVersion $version)
    {
        // Ensure version belongs to page
        if ($version->page_id !== $page->id) {
            abort(404);
        }

        $version->load('creator', 'ogImage');

        return response()->json([
            'id' => $version->id,
            'page_id' => $version->page_id,
            'title' => $version->title,
            'content' => $version->content,
            'description' => $version->description,
            'meta_description' => $version->meta_description,
            'meta_keywords' => $version->meta_keywords,
            'og_image_media_id' => $version->og_image_media_id,
            'was_published' => (bool) $version->was_published,
            'version_number' => $version->version_number,
            'change_reason' => $version->change_reason,
            'restored_from' => $version->restored_from,
            'created_at' => $version->created_at->toDateTimeString(),
            'creator' => $version->creator ? [
                'id' => $version->creator->id,
                'name' => $version->creator->name,
            ] : null,
        ]);
    }

    /**
     * Restore a page to a specific version.
     * Only Super Admin can restore versions.
     */
    public function restore(Request $request, Page $page, PageVersion $version)
    {
        // Ensure version belongs to page
        if ($version->page_id !== $page->id) {
            abort(404);
        }

        // Check authorization: only Super Admin can restore
        if (!auth()->user() || !auth()->user()->hasRole('Super Admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Restore version to page
        $version->restoreToPage(auth()->id());

        return redirect()->route('pages.edit', $page->id)->with('success', 'Page restored to version ' . $version->version_number);
    }
}
