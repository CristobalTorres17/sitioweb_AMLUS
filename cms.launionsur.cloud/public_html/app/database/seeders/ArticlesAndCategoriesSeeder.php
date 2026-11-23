<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticlesAndCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        // Remove any article categories that are not in this canonical list
        // (keeps only the rows we actually manage; leaves other types untouched)
        $canonicalSlugs = [
            'noticias-municipales',
            'comunicados',
            'anuncios',
            'proyectos',
            'galeria',
            'noticias',   // legacy slug kept for backward-compat with migrated articles
            'eventos',    // legacy slug kept for backward-compat with migrated articles
        ];

        DB::table('categories')
            ->where('type', Category::TYPE_ARTICLE)
            ->whereNotIn('slug', $canonicalSlugs)
            ->delete();

        $articleCategories = [
            ['name' => 'Noticias Municipales', 'slug' => 'noticias-municipales', 'color' => '#1d4ed8', 'icon' => 'newspaper',   'position' => 1],
            ['name' => 'Comunicados',          'slug' => 'comunicados',          'color' => '#dc2626', 'icon' => 'campaign',    'position' => 2],
            ['name' => 'Anuncios',             'slug' => 'anuncios',             'color' => '#f59e0b', 'icon' => 'announcement', 'position' => 3],
            ['name' => 'Proyectos',            'slug' => 'proyectos',            'color' => '#16a34a', 'icon' => 'construction', 'position' => 4],
            ['name' => 'Galería',              'slug' => 'galeria',              'color' => '#7c3aed', 'icon' => 'photo_library', 'position' => 5],
            // Legacy slugs — kept for backward-compatibility, no icon/color needed
            ['name' => 'Noticias',             'slug' => 'noticias',             'color' => null,      'icon' => null,          'position' => 6],
            ['name' => 'Eventos',              'slug' => 'eventos',              'color' => null,      'icon' => null,          'position' => 7],
        ];

        foreach ($articleCategories as $c) {
            Category::withoutGlobalScopes()->updateOrCreate(
                ['slug' => $c['slug'], 'type' => Category::TYPE_ARTICLE],
                [
                    'name'      => $c['name'],
                    'type'      => Category::TYPE_ARTICLE,
                    'color'     => $c['color'],
                    'icon'      => $c['icon'],
                    'position'  => $c['position'],
                    'is_active' => true,
                ],
            );
        }
    }
}
