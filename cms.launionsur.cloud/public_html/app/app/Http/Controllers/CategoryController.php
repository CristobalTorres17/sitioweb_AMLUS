<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /** Managed types — only article and event are handled here. */
    private const MANAGED_TYPES = [
        Category::TYPE_ARTICLE,
        Category::TYPE_EVENT,
    ];

    public function index()
    {
        $categories = Category::withoutGlobalScopes()
            ->whereIn('type', self::MANAGED_TYPES)
            ->orderBy('type')
            ->orderBy('position')
            ->get(['id', 'name', 'slug', 'type', 'description', 'icon', 'color', 'is_active', 'position', 'deleted_at'])
            ->map(fn(Category $c) => [
                'id'          => $c->id,
                'name'        => $c->name,
                'slug'        => $c->slug,
                'type'        => $c->type,
                'description' => $c->description,
                'icon'        => $c->icon,
                'color'       => $c->color,
                'is_active'   => (bool) $c->is_active,
                'position'    => (int) $c->position,
                'deleted_at'  => $c->deleted_at?->toDateTimeString(),
            ]);

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return Inertia::render('Categories/Create', [
            'types' => self::MANAGED_TYPES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:' . implode(',', self::MANAGED_TYPES),
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:20',
            'is_active'   => 'boolean',
            'position'    => 'nullable|integer|min:0',
        ]);

        $slug = $this->uniqueSlug(Str::slug($data['name']), $data['type']);

        Category::withoutGlobalScopes()->create([
            'name'        => $data['name'],
            'slug'        => $slug,
            'type'        => $data['type'],
            'description' => $data['description'] ?? null,
            'icon'        => $data['icon'] ?? null,
            'color'       => $data['color'] ?? null,
            'is_active'   => (bool) ($data['is_active'] ?? true),
            'position'    => $data['position'] ?? (Category::withoutGlobalScopes()
                ->where('type', $data['type'])
                ->max('position') + 1),
        ]);

        return redirect()->route('categories.index')->with('success', 'Categoría creada.');
    }

    public function edit(Category $category)
    {
        return Inertia::render('Categories/Edit', [
            'category' => [
                'id'          => $category->id,
                'name'        => $category->name,
                'slug'        => $category->slug,
                'type'        => $category->type,
                'description' => $category->description,
                'icon'        => $category->icon,
                'color'       => $category->color,
                'is_active'   => (bool) $category->is_active,
                'position'    => (int) $category->position,
            ],
            'types' => self::MANAGED_TYPES,
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:' . implode(',', self::MANAGED_TYPES),
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:20',
            'is_active'   => 'boolean',
            'position'    => 'nullable|integer|min:0',
        ]);

        $slug = $this->uniqueSlug(Str::slug($data['name']), $data['type'], $category->id);

        $category->update([
            'name'        => $data['name'],
            'slug'        => $slug,
            'type'        => $data['type'],
            'description' => $data['description'] ?? null,
            'icon'        => $data['icon'] ?? null,
            'color'       => $data['color'] ?? null,
            'is_active'   => (bool) ($data['is_active'] ?? true),
            'position'    => $data['position'] ?? $category->position,
        ]);

        return redirect()->route('categories.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Categoría eliminada.');
    }

    public function restore(Category $category)
    {
        $category->restore();

        return redirect()->route('categories.index')->with('success', 'Categoría restaurada.');
    }

    public function moveUp(Category $category)
    {
        $prev = Category::withoutGlobalScopes()
            ->where('type', $category->type)
            ->where('position', '<', $category->position)
            ->orderByDesc('position')
            ->first();

        if ($prev) {
            [$prev->position, $category->position] = [$category->position, $prev->position];
            $prev->save();
            $category->save();
        }

        return back();
    }

    public function moveDown(Category $category)
    {
        $next = Category::withoutGlobalScopes()
            ->where('type', $category->type)
            ->where('position', '>', $category->position)
            ->orderBy('position')
            ->first();

        if ($next) {
            [$next->position, $category->position] = [$category->position, $next->position];
            $next->save();
            $category->save();
        }

        return back();
    }

    private function uniqueSlug(string $base, string $type, ?int $excludeId = null): string
    {
        $slug = $base;
        $i    = 1;

        while (
            Category::withoutGlobalScopes()
            ->where('type', $type)
            ->where('slug', $slug)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
