<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckHistorialSchema extends Command
{
    protected $signature = 'legacy:check-historial-schema';
    protected $description = 'Check the historial table schema in legacy database';

    public function handle()
    {
        $this->info("\n📋 LEGACY HISTORIAL TABLE SCHEMA\n");
        $this->line(str_repeat("=", 80));

        try {
            $columns = DB::connection('legacy')->select("DESCRIBE historial");

            echo "Columns:\n";
            foreach ($columns as $col) {
                $null = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                $key = $col->Key ? "[{$col->Key}]" : '';
                $default = $col->Default ? "DEFAULT: {$col->Default}" : '';
                $info = trim("$null $key $default");
                $this->line(sprintf("  %-20s | %-30s | %s", $col->Field, $col->Type, $info));
            }

            // Get sample data
            $count = DB::connection('legacy')->table('historial')->count();
            $this->line("\nTotal records: $count");

            if ($count > 0) {
                $sample = DB::connection('legacy')->table('historial')->limit(1)->first();
                $this->line("\nSample data:");
                foreach ((array)$sample as $key => $value) {
                    $val = is_null($value) ? 'NULL' : (strlen((string)$value) > 50 ? substr((string)$value, 0, 47) . '...' : $value);
                    $this->line(sprintf("  %s = %s", $key, $val));
                }
            }

            $this->line("\n" . str_repeat("=", 80) . "\n");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
