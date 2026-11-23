<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CmsBlockRevision extends Model
{
    use HasFactory;

    protected $table = 'cms_block_revisions';

    protected $fillable = [
        'cms_block_id',
        'user_id',
        'name',
        'content',
        'excerpt',
        'data',
        'revision_number',
    ];

    protected $casts = [
        'data' => 'json',
    ];

    const UPDATED_AT = null; // Revisions are immutable

    public function block()
    {
        return $this->belongsTo(CmsBlock::class, 'cms_block_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function createFromBlock(CmsBlock $block)
    {
        return self::createFromSnapshot($block->toArray());
    }

    public static function createFromSnapshot(array $snapshot)
    {
        $blockId = $snapshot['id'] ?? ($snapshot['cms_block_id'] ?? null);

        $lastRevision = self::where('cms_block_id', $blockId)
            ->latest('revision_number')
            ->first();

        $nextNumber = $lastRevision ? $lastRevision->revision_number + 1 : 1;

        return self::create([
            'cms_block_id' => $blockId,
            'user_id' => Auth::id(),
            'name' => $snapshot['name'] ?? null,
            'content' => $snapshot['content'] ?? null,
            'excerpt' => $snapshot['excerpt'] ?? null,
            'data' => $snapshot,
            'revision_number' => $nextNumber,
        ]);
    }

    public function restore()
    {
        $this->block->update([
            'name' => $this->name,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'template_key' => data_get($this->data, 'template_key', $this->block->template_key),
            'zone' => data_get($this->data, 'zone', $this->block->zone),
            'template_data' => data_get($this->data, 'template_data', $this->block->template_data),
        ]);

        return $this->block;
    }
}
