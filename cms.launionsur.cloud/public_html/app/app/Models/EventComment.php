<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_id',
        'user_id',
        'author_name',
        'author_email',
        'content',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Event this comment belongs to
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * User who authored this comment (if authenticated)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for approved comments
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending moderation
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
