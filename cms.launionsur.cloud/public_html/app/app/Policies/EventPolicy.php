<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin', 'reviewer']);
    }

    public function view(User $user, Event $event): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin', 'reviewer']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function update(User $user, Event $event): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, Event $event): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, Event $event): bool
    {
        return $user->hasRole('super_admin');
    }
}
