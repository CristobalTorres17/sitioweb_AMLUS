<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocalDemoBlockSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            throw new \RuntimeException('Los datos de prueba requieren APP_ENV=local.');
        }

        DB::transaction(function (): void {
            if (DB::table('cms_blocks')->where('slug', 'bienvenida-local')->exists()) {
                return;
            }

            $id = DB::table('cms_blocks')->insertGetId([
                'name' => 'Bienvenida local',
                'slug' => 'bienvenida-local',
                'content' => '<p>Bloque de prueba publicado desde el CMS.</p>',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('cms_block_page')->insert([
                'cms_block_id' => $id,
                'page' => 'home',
                'position' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}
