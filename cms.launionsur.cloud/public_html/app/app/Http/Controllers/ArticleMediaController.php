<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Media;
use Illuminate\Support\Facades\DB;

class ArticleMediaController extends Controller
{
    public function attach(Request $request, Article $article)
    {
        $data = $request->validate([
            'media_ids' => 'required|array',
            'media_ids.*' => 'required|integer|exists:media,id',
        ]);

        DB::transaction(function () use ($article, $data) {
            $sync = [];
            foreach ($data['media_ids'] as $i => $id) {
                $sync[$id] = [
                    'position' => $i,
                    'is_primary' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // preserve existing pivot entries by merging
            $existing = $article->media()->pluck('media.id')->toArray();
            $article->media()->syncWithoutDetaching($sync);

            // find newly attached ids and increment usage_count
            $new = array_diff(array_keys($sync), $existing);
            if (!empty($new)) {
                foreach ($new as $nid) {
                    \App\Models\Media::where('id', $nid)->increment('usage_count');
                }
            }
        });

        return response()->json(['status' => 'ok']);
    }

    public function detach(Request $request, Article $article, Media $media)
    {
        $article->media()->detach($media->id);
        return response()->json(['status' => 'ok']);
    }

    public function primary(Request $request, Article $article, Media $media)
    {
        DB::transaction(function () use ($article, $media) {
            // clear others
            $article->media()->newPivotStatement()
                ->where('article_id', $article->id)
                ->update(['is_primary' => false]);

            // set this one
            $article->media()->updateExistingPivot($media->id, ['is_primary' => true]);

            // update legacy image_path
            if ($media->path) {
                $article->image_path = $media->path;
                $article->save();
            }
        });

        return response()->json(['status' => 'ok']);
    }

    public function reorder(Request $request, Article $article)
    {
        $data = $request->validate([
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'required|integer|exists:media,id',
        ]);

        DB::transaction(function () use ($article, $data) {
            foreach ($data['ordered_ids'] as $pos => $id) {
                $article->media()->updateExistingPivot($id, ['position' => $pos]);
            }
        });

        return response()->json(['status' => 'ok']);
    }

    public function updateCaption(Request $request, Article $article, Media $media)
    {
        $data = $request->validate([
            'caption' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
        ]);

        $article->media()->updateExistingPivot($media->id, [
            'caption' => $data['caption'] ?? null,
            'alt_text' => $data['alt_text'] ?? null,
        ]);

        return response()->json(['status' => 'ok']);
    }
}
