<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaTag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'created_by',
    ];

    /**
     * User who created this tag
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Media tagged with this tag
     */
    public function media()
    {
        return $this->belongsToMany(Media::class, 'media_tag', 'media_tags_id', 'media_id')
            ->withTimestamps();
    }
}
