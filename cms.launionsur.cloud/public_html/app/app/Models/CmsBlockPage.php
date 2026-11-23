<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsBlockPage extends Model
{
    use HasFactory;

    protected $table = 'cms_block_page';

    protected $fillable = [
        'cms_block_id',
        'page',
        'position',
    ];

    public $timestamps = true;

    public function block()
    {
        return $this->belongsTo(CmsBlock::class, 'cms_block_id');
    }
}
