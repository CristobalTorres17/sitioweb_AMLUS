<?php

namespace App\Http\Controllers;

use App\Models\MediaCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaCollectionController extends Controller
{
    /**
     * GET /media-collections
     * Returns full collection tree as JSON.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => MediaCollection::tree(),
        ]);
    }

    /**
     * POST /media-collections
     * Create a new folder.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', MediaCollection::class);

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:media_collections,id'],
            'color'     => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'icon'      => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $collection = MediaCollection::create([
            ...$data,
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id'          => $collection->id,
                'name'        => $collection->name,
                'slug'        => $collection->slug,
                'parent_id'   => $collection->parent_id,
                'color'       => $collection->color,
                'icon'        => $collection->icon,
                'media_count' => 0,
                'children'    => [],
            ],
        ], 201);
    }

    /**
     * PATCH /media-collections/{mediaCollection}
     * Rename or re-color a folder.
     */
    public function update(Request $request, MediaCollection $mediaCollection): JsonResponse
    {
        $this->authorize('update', $mediaCollection);

        $data = $request->validate([
            'name'      => ['sometimes', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:media_collections,id'],
            'color'     => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'icon'      => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $mediaCollection->update($data);

        return response()->json(['success' => true, 'data' => $mediaCollection->fresh()]);
    }

    /**
     * DELETE /media-collections/{mediaCollection}
     * Delete a folder. Media inside is moved to the parent (or uncollected).
     * Child sub-folders are also re-parented.
     */
    public function destroy(MediaCollection $mediaCollection): JsonResponse
    {
        $this->authorize('delete', $mediaCollection);

        // Don't allow deleting root auto-seeded folders that have a slug
        // that is used by entities (safety guard — soft-delete handles the rest).

        // Move contained media up to parent (or uncollected)
        $mediaCollection->media()->update(['collection_id' => $mediaCollection->parent_id]);

        // Move child folders up to parent
        $mediaCollection->children()->update(['parent_id' => $mediaCollection->parent_id]);

        $mediaCollection->delete();

        return response()->json(['success' => true]);
    }
}
