<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const TYPE_ARTICLE = 'article';
    public const TYPE_EVENT  = 'event';

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'icon',
        'color',
        'is_active',
        'position',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position' => 'integer',
    ];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
