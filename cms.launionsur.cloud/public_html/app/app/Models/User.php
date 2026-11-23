<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'last_login_at',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'deleted_at' => 'datetime',
            'locked_until' => 'datetime',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && strtolower($this->role->name) === strtolower($roleName);
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roleNames): bool
    {
        if (! $this->role) {
            return false;
        }
        $current = strtolower($this->role->name);
        $lower = array_map(fn($r) => strtolower($r), $roleNames);
        return in_array($current, $lower, true);
    }

    /**
     * Check if user (via assigned role) has a specific permission name.
     */
    public function hasPermission(string $permissionName): bool
    {
        // Accept both hyphen and underscore role names for backward compatibility
        if ($this->hasAnyRole(['super-admin', 'super_admin'])) {
            return true;
        }

        if (! $this->role) {
            return false;
        }

        $perms = $this->role->permissions()->pluck('name')->map(fn($n) => strtolower($n))->toArray();
        return in_array(strtolower($permissionName), $perms, true);
    }

    /**
     * User profile (extended info: phone, avatar, bio, etc.)
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Events authored by this user
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'author_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
