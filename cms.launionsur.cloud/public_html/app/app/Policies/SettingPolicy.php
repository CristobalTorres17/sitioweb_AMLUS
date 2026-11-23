<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Setting;

class SettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('edit');
    }

    public function view(User $user, Setting $setting): bool
    {
        return $user->hasPermission('edit');
    }

    public function update(User $user, Setting $setting): bool
    {
        return $user->hasPermission('edit');
    }
}
