<?php

namespace App\Policies;

use App\Models\CmsBlock;
use App\Models\User;

class CmsBlockPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function view(User $user, CmsBlock $cmsBlock): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function update(User $user, CmsBlock $cmsBlock): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function delete(User $user, CmsBlock $cmsBlock): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, CmsBlock $cmsBlock): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, CmsBlock $cmsBlock): bool
    {
        return $user->hasRole('super_admin');
    }
}
