<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Render admin users index with pagination and optional search.
     */
    public function adminIndex(Request $request)
    {
        $this->authorize('viewAny', User::class);
        $perPage = (int) $request->input('per_page', 20);

        $users = User::with('role')
            ->when($request->input('search'), function ($q, $search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => Role::all(),
        ]);
    }

    /**
     * Render create user page.
     */
    public function adminCreate()
    {
        $this->authorize('create', User::class);
        return Inertia::render('Users/Create', [
            'roles' => Role::all(),
        ]);
    }

    /**
     * Render edit user page.
     */
    public function adminEdit(User $user)
    {
        $this->authorize('update', $user);
        return Inertia::render('Users/Edit', [
            'user' => $user->load('role'),
            'roles' => Role::all(),
        ]);
    }
}
