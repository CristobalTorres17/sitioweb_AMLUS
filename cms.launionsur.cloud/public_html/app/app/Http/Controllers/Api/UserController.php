<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AuditService;
use App\Services\SensitiveChangeDetector;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function store(Request $request)
    {
        if (!auth()->user() || auth()->user()->role->name !== 'super_admin') {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => 'required|confirmed|min:8',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        if (!empty($data['role_id'])) {
            $user->role_id = $data['role_id'];
            $user->save();
        }

        $newValues = ['id' => $user->id, 'email' => $user->email];
        $isSensitive = SensitiveChangeDetector::detectFromValues('User', [], $newValues);
        $reason = SensitiveChangeDetector::getSensitiveReason('User', AuditService::detectChangedFields([], $newValues));

        AuditService::log($user, 'user.create', [], $newValues, $isSensitive, $reason);

        return response()->json($user, 201);
    }

    public function index(Request $request)
    {
        // allow only super_admin to list users
        if (!auth()->user() || auth()->user()->role->name !== 'super_admin') {
            abort(403);
        }

        $perPage = (int) $request->input('per_page', 15);
        $users = User::with('role')->paginate($perPage);

        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        // allow super admin or the user themself
        if (!(auth()->user() && (auth()->user()->role->name === 'super_admin' || auth()->id() === $user->id))) {
            abort(403);
        }
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        if (!(auth()->user() && (auth()->user()->role->name === 'super_admin' || auth()->id() === $user->id))) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role_id' => 'nullable|exists:roles,id',
        ]);

        $oldValues = $user->getOriginal();
        $user->update($data);

        if (array_key_exists('role_id', $data)) {
            $user->role_id = $data['role_id'];
            $user->save();
        }

        $newValues = $user->toArray();
        $changedFields = AuditService::detectChangedFields($oldValues, $newValues);
        $isSensitive = SensitiveChangeDetector::detect('User', $changedFields);
        $reason = SensitiveChangeDetector::getSensitiveReason('User', $changedFields);

        AuditService::log($user, 'user.update', $oldValues, $newValues, $isSensitive, $reason);

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if (!auth()->user() || auth()->user()->role->name !== 'super_admin') {
            abort(403);
        }
        $oldValues = $user->toArray();
        $user->delete();

        $isSensitive = SensitiveChangeDetector::detectFromValues('User', $oldValues, []);
        $reason = SensitiveChangeDetector::getSensitiveReason('User', AuditService::detectChangedFields($oldValues, []));

        AuditService::log($user, 'user.delete', $oldValues, [], $isSensitive, $reason);

        return response()->json(['message' => 'User deleted']);
    }

    public function changePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($user->id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Invalid current password'], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        $newValues = ['id' => $user->id];
        $isSensitive = true;
        $reason = 'Password change';

        AuditService::log($user, 'user.change_password', [], $newValues, $isSensitive, $reason);

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function assignRole(Request $request, $id)
    {
        if (!auth()->user() || auth()->user()->role->name !== 'super_admin') {
            abort(403);
        }

        $user = User::findOrFail($id);

        $data = $request->validate(['role_id' => 'required|exists:roles,id']);

        $oldValues = $user->toArray();
        $user->role_id = $data['role_id'];
        $user->save();

        $newValues = $user->toArray();
        $changedFields = AuditService::detectChangedFields($oldValues, $newValues);
        $isSensitive = SensitiveChangeDetector::detect('User', $changedFields);
        $reason = SensitiveChangeDetector::getSensitiveReason('User', $changedFields);

        AuditService::log($user, 'user.assign_role', $oldValues, $newValues, $isSensitive, $reason);

        return response()->json(['message' => 'Role assigned successfully']);
    }
}
