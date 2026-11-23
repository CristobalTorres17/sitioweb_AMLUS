<?php

namespace App\Http\Controllers;

use App\Models\CmsBlock;
use App\Models\CmsBlockRevision;
use App\Models\CmsBlockPage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Media;
use App\Services\CmsBlockTemplateRenderer;
use Illuminate\Validation\Rule;

class CmsBlockController extends Controller
{
    public function __construct(private CmsBlockTemplateRenderer $templateRenderer) {}

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', 'all');

        $query = CmsBlock::withTrashed()
            ->with(['media', 'revisions', 'pages'])
            ->orderByDesc('created_at');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%')
                    ->orWhere('excerpt', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        if ($status === 'active') {
            $query->whereNull('deleted_at')->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->whereNull('deleted_at')->where('is_active', false);
        } elseif ($status === 'trashed') {
            $query->onlyTrashed();
        }

        $blocks = $query->get();

        $stats = [
            'total' => CmsBlock::withTrashed()->count(),
            'active' => CmsBlock::where('is_active', true)->count(),
            'inactive' => CmsBlock::where('is_active', false)->count(),
            'trashed' => CmsBlock::onlyTrashed()->count(),
        ];

        return Inertia::render('CmsBlocks/Index', [
            'blocks' => $blocks,
            'pages' => config('cms.pages', []),
            'templates' => $this->templateRenderer->definitions(),
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
            'stats' => $stats,
        ]);
    }

    public function create(Request $request)
    {
        $pages = config('cms.pages', []);

        return Inertia::render('CmsBlocks/Create', [
            'pages' => $pages,
            'templates' => $this->templateRenderer->definitions(),
            'initialPage' => $request->query('page'),
            'initialZone' => $request->query('zone'),
        ]);
    }

    public function store(Request $request)
    {
        $templateKeys = $this->templateRenderer->keys();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_blocks,slug',
            'template_key' => ['nullable', 'string', Rule::in($templateKeys)],
            'zone' => 'nullable|string|max:255',
            'template_data' => 'nullable|array',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'pages' => 'nullable|array',
            'pages.*' => 'string',
        ]);

        $templateKey = $data['template_key'] ?? $this->templateRenderer->defaultTemplateKey();
        $templateData = $templateKey === 'rich_text'
            ? null
            : $this->templateRenderer->normalizeData($templateKey, $data['template_data'] ?? null);
        $content = $templateKey === 'rich_text'
            ? (string) ($data['content'] ?? '')
            : $this->templateRenderer->render($templateKey, $templateData, $data['content'] ?? null);

        $block = CmsBlock::create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? null,
            'template_key' => $templateKey,
            'zone' => $data['zone'] ?? $this->templateRenderer->defaultZone($templateKey),
            'template_data' => $templateData,
            'content' => $content,
            'excerpt' => $data['excerpt'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        if (!empty($data['pages'])) {
            foreach ($data['pages'] as $index => $page) {
                CmsBlockPage::create([
                    'cms_block_id' => $block->id,
                    'page' => $page,
                    'position' => $index,
                ]);
            }
        }

        CmsBlockRevision::createFromBlock($block);

        // sync media referenced in content into pivot table
        $this->syncMediaFromContent($block, $block->content);

        return redirect()->route('cms-blocks.index')->with('success', 'CMS Block created successfully.');
    }

    public function edit(CmsBlock $cmsBlock)
    {
        $pages = config('cms.pages', []);

        $block = CmsBlock::with('media', 'revisions', 'pages')->find($cmsBlock->id);

        return Inertia::render('CmsBlocks/Edit', [
            'block' => $block,
            'pages' => $pages,
            'templates' => $this->templateRenderer->definitions(),
        ]);
    }

    public function revisions(CmsBlock $cmsBlock)
    {
        $block = CmsBlock::with(['revisions.author'])->findOrFail($cmsBlock->id);

        return Inertia::render('CmsBlocks/Revisions', [
            'block' => $block,
            'revisions' => $block->revisions,
        ]);
    }

    public function update(Request $request, CmsBlock $cmsBlock)
    {
        $templateKeys = $this->templateRenderer->keys();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('cms_blocks', 'slug')->ignore($cmsBlock->id),
            ],
            'template_key' => ['nullable', 'string', Rule::in($templateKeys)],
            'zone' => 'nullable|string|max:255',
            'template_data' => 'nullable|array',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'pages' => 'nullable|array',
            'pages.*' => 'string',
        ]);

        $templateKey = $data['template_key'] ?? $cmsBlock->template_key ?? $this->templateRenderer->defaultTemplateKey();
        $templateData = $templateKey === 'rich_text'
            ? null
            : $this->templateRenderer->normalizeData($templateKey, $data['template_data'] ?? null);
        $content = $templateKey === 'rich_text'
            ? (string) ($data['content'] ?? $cmsBlock->content)
            : $this->templateRenderer->render($templateKey, $templateData, $data['content'] ?? null);

        $cmsBlock->name = $data['name'];
        $cmsBlock->slug = $data['slug'] ?? $cmsBlock->slug;
        $cmsBlock->template_key = $templateKey;
        $cmsBlock->zone = $data['zone'] ?? $cmsBlock->zone ?? $this->templateRenderer->defaultZone($templateKey);
        $cmsBlock->template_data = $templateData;
        $cmsBlock->content = $content;
        $cmsBlock->excerpt = $data['excerpt'] ?? null;
        $cmsBlock->is_active = (bool) ($data['is_active'] ?? true);
        $cmsBlock->save();

        // replace pages
        CmsBlockPage::where('cms_block_id', $cmsBlock->id)->delete();
        if (!empty($data['pages'])) {
            foreach ($data['pages'] as $index => $page) {
                CmsBlockPage::create([
                    'cms_block_id' => $cmsBlock->id,
                    'page' => $page,
                    'position' => $index,
                ]);
            }
        }

        // sync media referenced in content into pivot table
        $this->syncMediaFromContent($cmsBlock, $cmsBlock->content);

        return redirect()->route('cms-blocks.index')->with('success', 'CMS Block updated.');
    }

    public function destroy(CmsBlock $cmsBlock)
    {
        $cmsBlock->delete();

        return redirect()->route('cms-blocks.index')->with('success', 'CMS Block deleted.');
    }

    public function restore($id)
    {
        $cmsBlock = CmsBlock::withTrashed()->findOrFail($id);
        $cmsBlock->restore();

        return redirect()->route('cms-blocks.index')->with('success', 'CMS Block restored.');
    }

    public function restoreRevision(CmsBlock $cmsBlock, CmsBlockRevision $revision)
    {
        if ($revision->cms_block_id !== $cmsBlock->id) {
            abort(404);
        }

        $revision->restore();

        return redirect()->route('cms-blocks.revisions.index', $cmsBlock->id)->with('success', 'Revision restored.');
    }

    public function moveUp(Request $request, CmsBlock $cmsBlock)
    {
        $data = $request->validate([
            'page' => 'required|string',
        ]);

        $page = $data['page'];

        $current = CmsBlockPage::where('cms_block_id', $cmsBlock->id)->where('page', $page)->firstOrFail();

        if ($current->position <= 0) {
            return back();
        }

        $swap = CmsBlockPage::where('page', $page)->where('position', $current->position - 1)->first();

        DB::transaction(function () use ($current, $swap) {
            if ($swap) {
                $oldPos = $swap->position;
                $swap->position = $current->position;
                $swap->save();
            }

            $current->position = $current->position - 1;
            $current->save();
        });

        return back();
    }

    public function moveDown(Request $request, CmsBlock $cmsBlock)
    {
        $data = $request->validate([
            'page' => 'required|string',
        ]);

        $page = $data['page'];

        $current = CmsBlockPage::where('cms_block_id', $cmsBlock->id)->where('page', $page)->firstOrFail();

        $maxPos = CmsBlockPage::where('page', $page)->max('position');
        if ($current->position >= $maxPos) {
            return back();
        }

        $swap = CmsBlockPage::where('page', $page)->where('position', $current->position + 1)->first();

        DB::transaction(function () use ($current, $swap) {
            if ($swap) {
                $oldPos = $swap->position;
                $swap->position = $current->position;
                $swap->save();
            }

            $current->position = $current->position + 1;
            $current->save();
        });

        return back();
    }

    protected function syncMediaFromContent(CmsBlock $block, $content)
    {
        if (empty($content)) {
            return;
        }

        // parse img tags
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content);
        $imgs = $dom->getElementsByTagName('img');

        $attached = [];

        foreach ($imgs as $img) {
            if (!$img instanceof \DOMElement) {
                continue;
            }

            $src = $img->getAttribute('src');
            $alt = $img->getAttribute('alt') ?: null;
            $caption = $img->getAttribute('data-caption') ?: null;

            if (empty($src)) continue;

            // try to find media by public URL or by stored url field
            $media = Media::all()->first(function ($m) use ($src) {
                $public = Storage::url($m->path);
                return $public === $src || ($m->url ?? null) === $src;
            });

            if (!$media) continue;

            $attached[$media->id] = [
                'position' => 0,
                'caption' => $caption,
                'alt_text' => $alt,
            ];
        }

        if (!empty($attached)) {
            // compute positions for new attachments
            $max = $block->media()->max('cms_block_media.position') ?? 0;
            $i = $max + 1;
            $sync = [];
            foreach ($attached as $mediaId => $meta) {
                $sync[$mediaId] = array_merge($meta, ['position' => $i++]);
            }

            // determine which will be newly attached
            $existing = $block->media()->pluck('media.id')->toArray();

            $block->media()->syncWithoutDetaching($sync);

            $new = array_diff(array_keys($sync), $existing);
            if (!empty($new)) {
                foreach ($new as $nid) {
                    \App\Models\Media::where('id', $nid)->increment('usage_count');
                }
            }
        }
    }
}
