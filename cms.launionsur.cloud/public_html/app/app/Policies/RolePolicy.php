<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function view(User $user, Role $model): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function update(User $user, Role $model): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function delete(User $user, Role $model): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, Role $model): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, Role $model): bool
    {
        return $user->hasRole('super_admin');
    }
}
