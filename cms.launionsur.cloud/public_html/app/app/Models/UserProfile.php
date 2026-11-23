<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'phone',
        'mobile',
        'bio',
        'position',
        'department',
        'avatar_id',
        'preferences',
    ];

    protected $casts = [
        'preferences' => 'array',
    ];

    /**
     * User associated with this profile
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Avatar image
     */
    public function avatar()
    {
        return $this->belongsTo(Media::class, 'avatar_id');
    }
}
