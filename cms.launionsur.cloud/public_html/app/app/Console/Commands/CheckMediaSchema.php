<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckMediaSchema extends Command
{
    protected $signature = 'db:check-media-schema';
    protected $description = 'Check the media table schema';

    public function handle()
    {
        $this->info("\n📋 MEDIA TABLE SCHEMA\n");
        $this->line(str_repeat("=", 80));

        try {
            $columns = DB::select("DESCRIBE media");

            foreach ($columns as $col) {
                $null = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                $key = $col->Key ? "[{$col->Key}]" : '';
                $default = $col->Default ? "DEFAULT: {$col->Default}" : '';
                $info = trim("$null $key $default");
                $this->line(sprintf("  %-20s | %-30s | %s", $col->Field, $col->Type, $info));
            }

            $this->line("\n" . str_repeat("=", 80) . "\n");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
