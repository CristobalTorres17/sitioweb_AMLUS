<?php

namespace App\Policies;

use App\Models\Banner;
use App\Models\User;

class BannerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function view(User $user, Banner $banner): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function update(User $user, Banner $banner): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function delete(User $user, Banner $banner): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, Banner $banner): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, Banner $banner): bool
    {
        return $user->hasRole('super_admin');
    }
}
