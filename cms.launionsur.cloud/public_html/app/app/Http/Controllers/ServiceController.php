<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceRequirement;
use App\Models\ServiceStep;
use Illuminate\Http\Request;
use App\Http\Requests\ServiceStoreRequest;
use App\Http\Requests\ServiceUpdateRequest;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Service::class);
        $services = Service::with('category')
            ->withCount(['steps', 'requirements'])
            ->orderBy('order')
            ->paginate(20);

        return Inertia::render('Services/Index', [
            'services' => $services,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Service::class);
        $categories = ServiceCategory::whereIn('type', ServiceCategory::managedTypes())
            ->orderBy('type')
            ->orderBy('position')
            ->get();
        return Inertia::render('Services/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(ServiceStoreRequest $request)
    {
        $this->authorize('create', Service::class);
        $data = $request->validated();
        $kind = $data['kind'] ?? Service::KIND_SERVICE;

        // generate unique slug within category
        $base = Str::slug($data['title']);
        $slug = $base;
        $i = 1;
        $categoryId = $data['category_id'] ?? null;
        while (Service::where('slug', $slug)->where('category_id', $categoryId)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $service = Service::create([
            'kind' => $kind,
            'title' => $data['title'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'category_id' => $categoryId,
            'estimated_time' => $data['estimated_time'] ?? null,
            'cost' => $data['cost'] ?? null,
            'unit' => $data['unit'] ?? null,
            'schedule' => $data['schedule'] ?? null,
            'documentation' => $data['documentation'] ?? null,
            'icon' => $data['icon'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'is_featured' => (bool) ($data['is_featured'] ?? false),
            'featured_order' => (int) ($data['featured_order'] ?? 0),
            'order' => 0,
        ]);

        $this->syncStepsAndRequirements($service, $data['steps'] ?? [], $data['requirements'] ?? []);

        if ($request->has('media') && is_array($request->input('media'))) {
            $this->syncMedia($service, $request->input('media'));
        }

        return redirect()->route('services.index')->with('success', 'Service created');
    }

    public function edit(Service $service)
    {
        $this->authorize('view', $service);
        $categories = ServiceCategory::whereIn('type', ServiceCategory::managedTypes())
            ->orderBy('type')
            ->orderBy('position')
            ->get();
        $service->load(['category', 'steps', 'requirements', 'media']);

        return Inertia::render('Services/Edit', [
            'service' => $service->append([]),  // ensure relations are serialized
            'categories' => $categories,
        ]);
    }

    public function update(ServiceUpdateRequest $request, Service $service)
    {
        $this->authorize('update', $service);
        $data = $request->validated();
        $kind = $data['kind'] ?? Service::KIND_SERVICE;

        // generate unique slug within category, excluding current service
        $base = Str::slug($data['title']);
        $slug = $base;
        $i = 1;
        $categoryId = $data['category_id'] ?? null;
        while (Service::where('slug', $slug)->where('category_id', $categoryId)->where('id', '!=', $service->id)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $service->update([
            'kind' => $kind,
            'title' => $data['title'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'category_id' => $categoryId,
            'estimated_time' => $data['estimated_time'] ?? null,
            'cost' => $data['cost'] ?? null,
            'unit' => $data['unit'] ?? null,
            'schedule' => $data['schedule'] ?? null,
            'documentation' => $data['documentation'] ?? null,
            'icon' => $data['icon'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'is_featured' => (bool) ($data['is_featured'] ?? false),
            'featured_order' => (int) ($data['featured_order'] ?? 0),
        ]);

        $service->steps()->delete();
        $service->requirements()->delete();

        $this->syncStepsAndRequirements($service, $data['steps'] ?? [], $data['requirements'] ?? []);

        if ($request->has('media') && is_array($request->input('media'))) {
            $this->syncMedia($service, $request->input('media'));
        }

        return redirect()->route('services.index')->with('success', 'Service updated');
    }

    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Service deleted');
    }

    public function moveStepUp(Service $service, ServiceStep $step)
    {
        $this->authorize('update', $service);
        if ($step->service_id !== $service->id) {
            abort(404);
        }

        $steps = $service->steps()->orderBy('sort_order')->get();
        $ids = $steps->pluck('id')->toArray();
        $pos = array_search($step->id, $ids, true);
        if ($pos === false || $pos === 0) {
            return back();
        }
        // swap
        [$ids[$pos - 1], $ids[$pos]] = [$ids[$pos], $ids[$pos - 1]];
        $this->reindexSteps($service, $ids);

        return back();
    }

    public function moveStepDown(Service $service, ServiceStep $step)
    {
        $this->authorize('update', $service);
        if ($step->service_id !== $service->id) {
            abort(404);
        }

        $steps = $service->steps()->orderBy('sort_order')->get();
        $ids = $steps->pluck('id')->toArray();
        $pos = array_search($step->id, $ids, true);
        if ($pos === false || $pos === count($ids) - 1) {
            return back();
        }
        // swap
        [$ids[$pos], $ids[$pos + 1]] = [$ids[$pos + 1], $ids[$pos]];
        $this->reindexSteps($service, $ids);

        return back();
    }

    public function moveRequirementUp(Service $service, ServiceRequirement $requirement)
    {
        $this->authorize('update', $service);
        if ($requirement->service_id !== $service->id) {
            abort(404);
        }

        $items = $service->requirements()->orderBy('sort_order')->get();
        $ids = $items->pluck('id')->toArray();
        $pos = array_search($requirement->id, $ids, true);
        if ($pos === false || $pos === 0) {
            return back();
        }
        [$ids[$pos - 1], $ids[$pos]] = [$ids[$pos], $ids[$pos - 1]];
        $this->reindexRequirements($service, $ids);

        return back();
    }

    public function moveRequirementDown(Service $service, ServiceRequirement $requirement)
    {
        $this->authorize('update', $service);
        if ($requirement->service_id !== $service->id) {
            abort(404);
        }

        $items = $service->requirements()->orderBy('sort_order')->get();
        $ids = $items->pluck('id')->toArray();
        $pos = array_search($requirement->id, $ids, true);
        if ($pos === false || $pos === count($ids) - 1) {
            return back();
        }
        [$ids[$pos], $ids[$pos + 1]] = [$ids[$pos + 1], $ids[$pos]];
        $this->reindexRequirements($service, $ids);

        return back();
    }

    public function reorderSteps(Request $request, Service $service)
    {
        $this->authorize('update', $service);
        $validated = $request->validate([
            'step_ids' => 'required|array',
            'step_ids.*' => 'integer|exists:service_steps,id',
        ]);

        // Verify all steps belong to this service
        $stepIds = $validated['step_ids'];
        $serviceStepIds = $service->steps()->pluck('id')->toArray();
        foreach ($stepIds as $stepId) {
            if (!in_array($stepId, $serviceStepIds, true)) {
                abort(403, 'Step does not belong to this service');
            }
        }

        $this->reindexSteps($service, $stepIds);

        return response()->json(['success' => true]);
    }

    public function reorderRequirements(Request $request, Service $service)
    {
        $this->authorize('update', $service);
        $validated = $request->validate([
            'requirement_ids' => 'required|array',
            'requirement_ids.*' => 'integer|exists:service_requirements,id',
        ]);

        // Verify all requirements belong to this service
        $requirementIds = $validated['requirement_ids'];
        $serviceRequirementIds = $service->requirements()->pluck('id')->toArray();
        foreach ($requirementIds as $requirementId) {
            if (!in_array($requirementId, $serviceRequirementIds, true)) {
                abort(403, 'Requirement does not belong to this service');
            }
        }

        $this->reindexRequirements($service, $requirementIds);

        return response()->json(['success' => true]);
    }

    private function reindexSteps(Service $service, array $orderedIds): void
    {
        foreach (array_values($orderedIds) as $index => $id) {
            ServiceStep::where('id', $id)->update(['sort_order' => $index + 1]);
        }
    }

    private function reindexRequirements(Service $service, array $orderedIds): void
    {
        foreach (array_values($orderedIds) as $index => $id) {
            ServiceRequirement::where('id', $id)->update(['sort_order' => $index + 1]);
        }
    }

    protected function syncMedia(Service $service, array $mediaArray): void
    {
        DB::transaction(function () use ($service, $mediaArray) {
            $syncData = [];
            $primaryMediaId = null;

            foreach ($mediaArray as $i => $m) {
                $mediaId = isset($m['media_id']) ? intval($m['media_id']) : (isset($m['id']) ? intval($m['id']) : null);
                if (!$mediaId) continue;

                $isPrimary = !empty($m['is_primary']);
                if ($isPrimary) $primaryMediaId = $mediaId;

                $syncData[$mediaId] = [
                    'is_primary' => $isPrimary,
                    'position'   => isset($m['position']) ? intval($m['position']) : $i,
                    'caption'    => $m['caption'] ?? null,
                    'alt_text'   => $m['alt_text'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $service->media()->sync($syncData);

            if ($primaryMediaId) {
                DB::table('service_media')->where('service_id', $service->id)->update(['is_primary' => false]);
                DB::table('service_media')->where('service_id', $service->id)->where('media_id', $primaryMediaId)->update(['is_primary' => true]);
            }
        });
    }

    private function syncStepsAndRequirements(Service $service, array $steps, array $requirements): void
    {
        foreach (array_values($steps) as $index => $step) {
            ServiceStep::create([
                'service_id' => $service->id,
                'title' => $step['title'],
                'description' => $step['description'] ?? null,
                'sort_order' => $index + 1,
            ]);
        }

        foreach (array_values($requirements) as $index => $requirement) {
            ServiceRequirement::create([
                'service_id' => $service->id,
                'title' => $requirement['title'],
                'description' => $requirement['description'] ?? null,
                'is_basic' => (bool) ($requirement['is_basic'] ?? true),
                'sort_order' => $index + 1,
            ]);
        }
    }
}
