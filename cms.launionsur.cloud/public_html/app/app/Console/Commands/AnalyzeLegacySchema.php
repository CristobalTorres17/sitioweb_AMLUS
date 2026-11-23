<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AnalyzeLegacySchema extends Command
{
    protected $signature = 'legacy:analyze-schema';
    protected $description = 'Analyze legacy database schema';

    public function handle()
    {
        $this->info("\n🔍 LEGACY DATABASE SCHEMA ANALYSIS\n");
        $this->line(str_repeat("=", 100));

        $schemas = [
            'adm_usuarios' => 'Users table',
            'adm_rol' => 'Roles table',
            'noticias' => 'News/Articles table',
            'banner' => 'Banners table',
            'imagenes' => 'Images table',
            'archivos' => 'Files/Documents table',
            'videos' => 'Videos table',
            'album' => 'Photo Albums',
            'submenu' => 'Menu items',
        ];

        foreach ($schemas as $table => $description) {
            try {
                $this->line("\n📋 $table ($description)");
                $this->line(str_repeat("-", 100));

                $columns = DB::connection('legacy')->select("DESCRIBE $table");

                $this->line("Columns:");
                foreach ($columns as $col) {
                    $null = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                    $key = $col->Key ? "[{$col->Key}]" : '';
                    $default = $col->Default ? "DEFAULT: {$col->Default}" : '';
                    $info = trim("$null $key $default");
                    $this->line(sprintf("  %-25s | %-30s | %s", $col->Field, $col->Type, $info));
                }

                // Get sample data
                $count = DB::connection('legacy')->table($table)->count();
                $this->line("\nRecords: $count");

                if ($count > 0) {
                    $sample = DB::connection('legacy')->table($table)->limit(1)->first();
                    $this->line("Sample:", 'comment');
                    foreach ((array)$sample as $key => $value) {
                        $val = is_null($value) ? 'NULL' : (strlen((string)$value) > 50 ? substr((string)$value, 0, 47) . '...' : $value);
                        $this->line(sprintf("  %s = %s", $key, $val), 'comment');
                    }
                }
            } catch (\Exception $e) {
                $this->error("Error analyzing $table: " . $e->getMessage());
            }
        }

        $this->line("\n" . str_repeat("=", 100) . "\n");
    }
}
