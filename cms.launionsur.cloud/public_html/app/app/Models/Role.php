<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    // Allow setting a machine name, a display name and an optional description
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * Permissions assigned to this role (many-to-many)
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Users that have this role (many-to-many)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }
}
