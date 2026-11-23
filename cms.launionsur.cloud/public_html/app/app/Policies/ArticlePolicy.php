<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin', 'reviewer']);
    }

    public function view(User $user, Article $article): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin', 'reviewer']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function update(User $user, Article $article): bool
    {
        return $user->hasAnyRole(['editor', 'super_admin']);
    }

    public function delete(User $user, Article $article): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, Article $article): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, Article $article): bool
    {
        return $user->hasRole('super_admin');
    }
}
