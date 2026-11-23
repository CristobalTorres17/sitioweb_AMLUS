<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarkOurMigrationsSeeder extends Seeder
{
    public function run()
    {
        $migrations = [
            '2026_04_05_000001_create_settings_table',
            '2026_04_05_000002_create_menus_tables',
            '2026_04_05_000003_create_roles_permissions_tables',
        ];

        $max = DB::table('migrations')->max('batch');
        $batch = $max ? $max : 1;

        foreach ($migrations as $migration) {
            $exists = DB::table('migrations')->where('migration', $migration)->exists();
            if (! $exists) {
                DB::table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => $batch,
                ]);
            }
        }
    }
}
