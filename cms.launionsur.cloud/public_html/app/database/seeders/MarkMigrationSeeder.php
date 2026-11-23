<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarkMigrationSeeder extends Seeder
{
    public function run()
    {
        $migration = '2026_04_02_030843_create_article_media_table';
        $max = DB::table('migrations')->max('batch');
        $batch = $max ? $max : 1;

        // If migration row already exists, do nothing
        $exists = DB::table('migrations')->where('migration', $migration)->exists();
        if (! $exists) {
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $batch,
            ]);
        }
    }
}
