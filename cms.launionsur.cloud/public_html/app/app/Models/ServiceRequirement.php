<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'title',
        'description',
        'is_basic',
        'sort_order',
    ];

    protected $casts = [
        'is_basic' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
