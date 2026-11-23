<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ArticleMedia extends Pivot
{
    protected $table = 'article_media';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'article_id',
        'media_id',
        'is_primary',
        'position',
        'caption',
        'alt_text',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'position' => 'integer',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (is_null($model->position)) {
                $model->position = 0;
            }
        });
    }

    /**
     * Mark this pivot row as the primary image for the article.
     * Note: caller should perform this inside a DB transaction when needed.
     */
    public function setAsPrimary()
    {
        static::where('article_id', $this->article_id)->update(['is_primary' => false]);
        $this->is_primary = true;
        $this->save();
    }
}
