<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Permission::class);
        $perPage = (int) $request->get('per_page', 30);
        $permissions = Permission::orderBy('name')->paginate($perPage)->withQueryString();
        return Inertia::render('Permissions/Index', ['permissions' => $permissions]);
    }

    public function create()
    {
        $this->authorize('create', Permission::class);
        return Inertia::render('Permissions/Create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Permission::class);
        $data = $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'display_name' => 'nullable|string',
        ]);

        Permission::create($data);
        return redirect()->route('admin.permissions.index')->with('success', 'Permission created');
    }

    public function edit(Permission $permission)
    {
        $this->authorize('update', $permission);
        return Inertia::render('Permissions/Edit', ['permission' => $permission]);
    }

    public function update(Request $request, Permission $permission)
    {
        $this->authorize('update', $permission);
        $data = $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id,
            'display_name' => 'nullable|string',
        ]);
        $permission->update($data);
        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated');
    }

    public function destroy(Permission $permission)
    {
        $this->authorize('delete', $permission);
        $permission->delete();
        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted');
    }
}
