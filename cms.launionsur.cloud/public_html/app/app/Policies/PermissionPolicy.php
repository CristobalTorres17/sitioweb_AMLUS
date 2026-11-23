<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;

class PermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function view(User $user, Permission $model): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function update(User $user, Permission $model): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function delete(User $user, Permission $model): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, Permission $model): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, Permission $model): bool
    {
        return $user->hasRole('super_admin');
    }
}
