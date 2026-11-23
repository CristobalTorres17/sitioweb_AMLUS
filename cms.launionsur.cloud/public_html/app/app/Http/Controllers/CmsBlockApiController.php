<?php

namespace App\Http\Controllers;

use App\Models\CmsBlock;
use Illuminate\Support\Facades\Storage;

class CmsBlockApiController extends Controller
{
    /**
     * GET /api/cms-blocks/{page}
     */
    public function getByPage($page)
    {
        $blocks = CmsBlock::active()
            ->forPage($page)
            ->with(['media', 'pages' => function ($q) use ($page) {
                $q->where('page', $page);
            }])
            ->get()
            ->sortBy(function ($block) {
                return optional($block->pages->first())->position ?? 0;
            })
            ->values()
            ->map(function ($block) {
                return [
                    'id' => $block->id,
                    'slug' => $block->slug,
                    'name' => $block->name,
                    'content' => $block->content,
                    'excerpt' => $block->excerpt,
                    'media' => $block->media->map(function ($m) {
                        return [
                            'id' => $m->id,
                            'url' => isset($m->path) ? Storage::url($m->path) : ($m->url ?? null),
                            'caption' => $m->pivot->caption ?? null,
                            'alt_text' => $m->pivot->alt_text ?? null,
                        ];
                    })->values(),
                ];
            });

        return response()->json([
            'page' => $page,
            'blocks' => $blocks,
            'count' => $blocks->count(),
        ]);
    }

    /**
     * GET /api/cms-blocks/slug/{slug}
     */
    public function getBySlug($slug)
    {
        $block = CmsBlock::active()
            ->where('slug', $slug)
            ->with('media')
            ->first();

        if (!$block) {
            return response()->json(['error' => 'Block not found'], 404);
        }

        return response()->json([
            'id' => $block->id,
            'slug' => $block->slug,
            'name' => $block->name,
            'content' => $block->content,
            'excerpt' => $block->excerpt,
            'media' => $block->media->map(function ($m) {
                return [
                    'id' => $m->id,
                    'url' => isset($m->path) ? Storage::url($m->path) : ($m->url ?? null),
                    'caption' => $m->pivot->caption ?? null,
                    'alt_text' => $m->pivot->alt_text ?? null,
                ];
            })->values(),
        ]);
    }
}
