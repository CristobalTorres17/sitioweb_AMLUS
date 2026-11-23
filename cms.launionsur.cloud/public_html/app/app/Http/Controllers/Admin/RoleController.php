<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Role::class);
        $perPage = (int) $request->get('per_page', 20);
        $roles = Role::orderBy('name')->paginate($perPage)->withQueryString();
        return Inertia::render('Roles/Index', ['roles' => $roles]);
    }

    public function create()
    {
        $this->authorize('create', Role::class);
        $permissions = Permission::orderBy('name')->get(['id', 'name']);
        return Inertia::render('Roles/Create', ['permissions' => $permissions]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Role::class);
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'display_name' => 'nullable|string',
        ]);

        $role = Role::create($data);
        // attach permissions if provided
        if ($request->has('permissions') && is_array($request->input('permissions'))) {
            $role->permissions()->sync($request->input('permissions'));
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role created');
    }

    public function edit(Role $role)
    {
        $this->authorize('update', $role);
        $permissions = Permission::orderBy('name')->get(['id', 'name']);
        $assigned = $role->permissions()->pluck('permissions.id')->toArray();
        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions,
            'assigned' => $assigned,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('update', $role);
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'display_name' => 'nullable|string',
        ]);
        $role->update($data);
        // sync permissions
        $perms = $request->input('permissions', []);
        if (!is_array($perms)) {
            $perms = [];
        }
        $role->permissions()->sync($perms);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated');
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted');
    }
}
