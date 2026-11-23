<?php

namespace App\Policies;

use App\Models\MediaCollection;
use App\Models\User;

class MediaCollectionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function update(User $user, MediaCollection $collection): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function delete(User $user, MediaCollection $collection): bool
    {
        // Only super_admin can delete root-level collections (safety)
        if ($collection->parent_id === null) {
            return $user->hasRole('super_admin');
        }

        return $user->hasAnyRole(['editor', 'super_admin']);
    }
}
