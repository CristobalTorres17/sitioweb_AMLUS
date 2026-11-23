<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventGallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'media_id',
        'position',
        'caption',
    ];

    protected $table = 'event_galleries';

    /**
     * Event associated with this gallery item
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Media file
     */
    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
