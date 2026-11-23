<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\User;

class MediaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function view(User $user, Media $media): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function update(User $user, Media $media): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function delete(User $user, Media $media): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, Media $media): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, Media $media): bool
    {
        return $user->hasRole('super_admin');
    }
}
