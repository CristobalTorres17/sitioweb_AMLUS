<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Menu;

class MenuPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('edit');
    }

    public function view(User $user, Menu $menu): bool
    {
        return $user->hasPermission('edit');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('edit');
    }

    public function update(User $user, Menu $menu): bool
    {
        return $user->hasPermission('edit');
    }

    public function delete(User $user, Menu $menu): bool
    {
        return $user->hasPermission('edit');
    }
}
