<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function view(User $user, Service $service): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function update(User $user, Service $service): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, Service $service): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, Service $service): bool
    {
        return $user->hasRole('super_admin');
    }
}
