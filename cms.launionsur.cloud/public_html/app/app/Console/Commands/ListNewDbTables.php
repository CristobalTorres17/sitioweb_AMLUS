<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ListNewDbTables extends Command
{
    protected $signature = 'db:list-tables';
    protected $description = 'List all tables in the new database';

    public function handle()
    {
        $this->info("\n📊 NEW DATABASE TABLES\n");
        $this->line(str_repeat("=", 80));

        try {
            // Get all tables using Laravel Schema
            $tables = collect(DB::select('SHOW TABLES'))->pluck(DB::getTablePrefix() === '' ? 'Tables_in_' . env('DB_DATABASE') : 'Tables_' . str_replace('-', '_', env('DB_DATABASE')));

            // Alternative: Get from SHOW TABLES
            $connection = DB::getDefaultConnection();
            $database = DB::connection($connection)->getDatabaseName();

            $allTables = DB::select("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?", [$database]);

            $this->line("\nTotal tablas: " . count($allTables) . "\n");

            foreach ($allTables as $table) {
                $tableName = $table->TABLE_NAME;
                $count = DB::table($tableName)->count();

                // Get columns
                $columns = Schema::getColumnListing($tableName);

                $this->line(sprintf("  %-35s : %6d registros | %d columns", $tableName, $count, count($columns)));
            }

            $this->line("\n" . str_repeat("=", 80) . "\n");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
