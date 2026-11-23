<?php

namespace Database\Seeders;

use App\Models\MediaCollection;
use Illuminate\Database\Seeder;

/**
 * Seeds the five root media folders.
 * Run once after the media_collections table is created:
 *   php artisan db:seed --class=MediaCollectionSeeder
 *
 * These folders are the top-level categories visible in the Gallery sidebar.
 * Sub-folders are created automatically when Events / Articles are saved.
 */
class MediaCollectionSeeder extends Seeder
{
    public function run(): void
    {
        $roots = [
            ['name' => 'Eventos',    'color' => '#16a34a', 'icon' => 'calendar'],
            ['name' => 'Noticias',   'color' => '#2563eb', 'icon' => 'newspaper'],
            ['name' => 'Servicios',  'color' => '#9333ea', 'icon' => 'briefcase'],
            ['name' => 'Banners',    'color' => '#d97706', 'icon' => 'photo'],
            ['name' => 'General',    'color' => '#6366f1', 'icon' => 'folder'],
        ];

        foreach ($roots as $data) {
            MediaCollection::firstOrCreate(
                ['name' => $data['name'], 'parent_id' => null],
                ['color' => $data['color'], 'icon' => $data['icon']],
            );
        }

        $this->command->info('Root media collections seeded.');
    }
}
