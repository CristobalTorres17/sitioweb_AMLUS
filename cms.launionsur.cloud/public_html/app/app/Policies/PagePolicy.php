<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;

class PagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin', 'reviewer']);
    }

    public function view(User $user, Page $page): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin', 'reviewer']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function update(User $user, Page $page): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function delete(User $user, Page $page): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, Page $page): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, Page $page): bool
    {
        return $user->hasRole('super_admin');
    }
}
