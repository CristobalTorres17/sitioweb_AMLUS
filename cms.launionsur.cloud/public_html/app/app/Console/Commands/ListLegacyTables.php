<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ListLegacyTables extends Command
{
    protected $signature = 'legacy:list-tables';
    protected $description = 'List all tables in legacy database';

    public function handle()
    {
        $this->info("\n📋 LEGACY DATABASE TABLES\n");
        $this->line(str_repeat("=", 80));

        try {
            // Get all tables
            $tables = DB::connection('legacy')->select('SHOW TABLES');
            $dbName = 'cmslusur_old';
            $tableKey = 'Tables_in_' . $dbName;

            $allTables = [];
            foreach ($tables as $table) {
                $allTables[] = $table->$tableKey;
            }

            sort($allTables);

            $this->line("\nTotal tablas: " . count($allTables) . "\n");

            $totalRecords = 0;
            $tableInfo = [];

            foreach ($allTables as $table) {
                $count = DB::connection('legacy')->table($table)->count();
                $totalRecords += $count;
                $tableInfo[$table] = $count;
                $this->line(sprintf("  %-30s : %6d registros", $table, $count));
            }

            $this->line("\n" . str_repeat("=", 80));
            $this->line("TOTAL REGISTROS: $totalRecords");
            $this->line(str_repeat("=", 80) . "\n");

            // List migrated tables
            $this->line("✅ TABLAS YA MIGRADAS:");
            $migrated = [
                'usuarios' => 'users',
                'roles' => 'roles',
                'articulos' => 'articles',
                'categorias' => 'categories',
                'eventos' => 'events',
                'galeria' => 'media + event_galleries',
                'banners' => 'banners',
                'menu' => 'menus',
                'menu_item' => 'menu_items',
            ];

            foreach ($migrated as $legacy => $new) {
                if (isset($tableInfo[$legacy])) {
                    $this->line("  ✓ $legacy ({$tableInfo[$legacy]} records) → $new");
                    unset($tableInfo[$legacy]);
                }
            }

            // List remaining tables
            $this->line("\n❌ TABLAS PENDIENTES DE MIGRAR:");
            if (empty($tableInfo)) {
                $this->line("  ✅ TODAS LAS TABLAS MIGRADAS");
            } else {
                foreach ($tableInfo as $table => $count) {
                    $this->line("  ❌ $table ($count registros)");
                }
            }

            $this->line("");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
