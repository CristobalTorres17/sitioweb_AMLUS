<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventGallery extends Model
{
    protected $table = 'event_galleries';

    protected $fillable = [
        'event_id',
        'media_id',
        'position',
        'caption',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
