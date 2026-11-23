<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateMediaRefs extends Command
{
    protected $signature = 'media:migrate-refs {--apply : Actually process and import found files} {--paths=* : Additional paths to search}';
    protected $description = 'Scan DB for legacy media references and report matches (dry-run by default)';

    public function handle()
    {
        $this->info('Scanning database for legacy media references...');

        $candidates = [];

        // Articles: content and image_path
        $this->info('Scanning articles...');
        $articles = DB::table('articles')->select('id', 'content', 'image_path')->get();
        foreach ($articles as $a) {
            if ($a->image_path) {
                $candidates[] = ['source' => 'articles.image_path', 'id' => $a->id, 'ref' => $a->image_path];
            }
            if ($a->content) {
                preg_match_all('/src=["\'](?<url>[^"\']+)["\']/i', $a->content, $m);
                if (!empty($m['url'])) {
                    foreach ($m['url'] as $url) {
                        $candidates[] = ['source' => 'articles.content', 'id' => $a->id, 'ref' => $url];
                    }
                }
            }
        }

        // Events: featured image and content
        $this->info('Scanning events...');
        $events = DB::table('events')->select('id', 'featured_image_id', 'content')->get();
        foreach ($events as $e) {
            if ($e->featured_image_id) {
                $candidates[] = ['source' => 'events.featured_image_id', 'id' => $e->id, 'ref' => $e->featured_image_id];
            }
            if ($e->content) {
                preg_match_all('/src=["\'](?<url>[^"\']+)["\']/i', $e->content, $m);
                if (!empty($m['url'])) {
                    foreach ($m['url'] as $url) {
                        $candidates[] = ['source' => 'events.content', 'id' => $e->id, 'ref' => $url];
                    }
                }
            }
        }

        // CMS blocks: content
        $this->info('Scanning cms_blocks...');
        $blocks = DB::table('cms_blocks')->select('id', 'content')->get();
        foreach ($blocks as $b) {
            if ($b->content) {
                preg_match_all('/src=["\'](?<url>[^"\']+)["\']/i', $b->content, $m);
                if (!empty($m['url'])) {
                    foreach ($m['url'] as $url) {
                        $candidates[] = ['source' => 'cms_blocks.content', 'id' => $b->id, 'ref' => $url];
                    }
                }
            }
        }

        $this->info('Scanning pivot tables for explicit file names...');
        $candidates = array_merge($candidates, $this->scanPivotTable('event_galleries', 'event_galleries', ['event_id', 'id']));
        $candidates = array_merge($candidates, $this->scanPivotTable('service_media', 'service_media', ['service_id', 'id']));

        $this->info('Normalizing candidates and checking files...');

        $storagePaths = [
            storage_path('app/public/legacy/images'),
            storage_path('app/public'),
            base_path('backups'),
        ];
        $extra = $this->option('paths') ?: [];
        foreach ($extra as $p) {
            $storagePaths[] = base_path($p);
        }

        $found = [];
        $notFound = [];

        foreach ($candidates as $c) {
            $ref = $c['ref'];
            // if it's a URL, strip domain and query
            $refClean = preg_replace('#^https?://[^/]+/#i', '', $ref);
            $refClean = strtok($refClean, '?');
            $refClean = ltrim($refClean, '/');

            $matched = false;
            foreach ($storagePaths as $sp) {
                if (!is_dir($sp)) {
                    continue;
                }

                $try = $sp . DIRECTORY_SEPARATOR . $refClean;
                if (file_exists($try)) {
                    $found[] = array_merge($c, ['path' => $try]);
                    $matched = true;
                    break;
                }
                // also try basename search
                $basename = basename($refClean);
                $iter = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($sp));
                foreach ($iter as $file) {
                    if ($file->isFile() && $file->getFilename() === $basename) {
                        $found[] = array_merge($c, ['path' => $file->getPathname()]);
                        $matched = true;
                        break 2;
                    }
                }
            }
            if (!$matched) {
                $notFound[] = $c;
            }
        }

        $this->newLine();
        $this->info("Candidates: " . count($candidates));
        $this->info("Found: " . count($found));
        $this->info("Not found: " . count($notFound));

        if (!empty($found)) {
            $this->newLine();
            $this->info('Sample found items (first 50):');
            $rows = array_map(fn($f) => [$f['source'], $f['id'], $f['ref'], $f['path']], array_slice($found, 0, 50));
            $this->table(['source', 'id', 'ref', 'path'], $rows);
        }

        if (!empty($notFound)) {
            $this->newLine();
            $this->info('Sample not found items (first 50):');
            $rows = array_map(fn($f) => [$f['source'], $f['id'], $f['ref']], array_slice($notFound, 0, 50));
            $this->table(['source', 'id', 'ref'], $rows);
        }

        if ($this->option('apply')) {
            $this->warn('Apply option requested, but import processing is not yet implemented.');
        } else {
            $this->info('Dry-run complete. Re-run with --apply to process (not implemented).');
        }

        return 0;
    }

    /**
     * @param array<int, string> $idColumns
     * @return array<int, array{source:string,id:mixed,ref:string}>
     */
    private function scanPivotTable(string $table, string $sourcePrefix, array $idColumns): array
    {
        if (!Schema::hasTable($table)) {
            return [];
        }

        $rows = DB::table($table)->get();
        $results = [];

        foreach ($rows as $row) {
            $rowArray = (array) $row;
            $rowId = null;
            foreach ($idColumns as $idColumn) {
                if (array_key_exists($idColumn, $rowArray) && !empty($rowArray[$idColumn])) {
                    $rowId = $rowArray[$idColumn];
                    break;
                }
            }

            foreach ($rowArray as $column => $value) {
                if (!is_scalar($value) || $value === null || $value === '') {
                    continue;
                }

                if (is_string($value) && preg_match('/(image|path|file|url|src|media)/i', $column)) {
                    $results[] = [
                        'source' => $sourcePrefix . '.' . $column,
                        'id' => $rowId,
                        'ref' => $value,
                    ];
                }
            }
        }

        return $results;
    }
}
