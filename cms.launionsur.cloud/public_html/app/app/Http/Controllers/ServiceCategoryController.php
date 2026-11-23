<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::whereIn('type', ServiceCategory::managedTypes())
            ->orderBy('type')
            ->orderBy('position')
            ->paginate(20);
        return Inertia::render('Services/Categories', ['categories' => $categories]);
    }

    public function create()
    {
        return Inertia::render('Services/CategoryCreate');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(ServiceCategory::managedTypes())],
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $slug = Str::slug($data['name']);
        $i = 1;
        $base = $slug;
        while (ServiceCategory::where('slug', $slug)->where('type', $data['type'])->exists()) {
            $slug = $base . '-' . $i++;
        }

        $category = ServiceCategory::create([
            'name' => $data['name'],
            'slug' => $slug,
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'position' => ServiceCategory::max('position') + 1,
        ]);

        return redirect()->route('service-categories.index')->with('success', 'Category created');
    }

    public function edit(ServiceCategory $serviceCategory)
    {
        return Inertia::render('Services/CategoryEdit', ['category' => $serviceCategory]);
    }

    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(ServiceCategory::managedTypes())],
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $slug = Str::slug($data['name']);
        $i = 1;
        $base = $slug;
        while (ServiceCategory::where('slug', $slug)
            ->where('type', $data['type'])
            ->where('id', '!=', $serviceCategory->id)
            ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        $serviceCategory->update([
            'name' => $data['name'],
            'slug' => $slug,
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return redirect()->route('service-categories.index')->with('success', 'Category updated');
    }

    public function destroy(ServiceCategory $serviceCategory)
    {
        $serviceCategory->delete();
        return redirect()->route('service-categories.index')->with('success', 'Category deleted');
    }

    public function moveUp(ServiceCategory $serviceCategory)
    {
        $prev = ServiceCategory::where('position', '<', $serviceCategory->position)->orderBy('position', 'desc')->first();
        if ($prev) {
            [$prev->position, $serviceCategory->position] = [$serviceCategory->position, $prev->position];
            $prev->save();
            $serviceCategory->save();
        }
        return back();
    }

    public function moveDown(ServiceCategory $serviceCategory)
    {
        $next = ServiceCategory::where('position', '>', $serviceCategory->position)->orderBy('position')->first();
        if ($next) {
            [$next->position, $serviceCategory->position] = [$serviceCategory->position, $next->position];
            $next->save();
            $serviceCategory->save();
        }
        return back();
    }
}
