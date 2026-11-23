<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds the event/news categories for the municipality portal.
 *
 * These categories appear in:
 *  - CMS admin → Eventos → selector de categoría
 *  - Sitio público → /noticias → filtros de categoría
 *
 * They are stored in `categories` table with type = 'event',
 * separate from article categories (type = 'article').
 */
class EventCategoriesSeeder extends Seeder
{
    /** @var array<int, array{name: string, icon: string, color: string, position: int}> */
    private array $categories = [
        [
            'name'     => 'Infraestructura y Obras',
            'icon'     => 'construction',
            'color'    => '#f59e0b',
            'position' => 1,
        ],
        [
            'name'     => 'Salud Comunitaria',
            'icon'     => 'health_and_safety',
            'color'    => '#ef4444',
            'position' => 2,
        ],
        [
            'name'     => 'Educación',
            'icon'     => 'school',
            'color'    => '#3b82f6',
            'position' => 3,
        ],
        [
            'name'     => 'Cultura y Arte',
            'icon'     => 'palette',
            'color'    => '#8b5cf6',
            'position' => 4,
        ],
        [
            'name'     => 'Medio Ambiente',
            'icon'     => 'eco',
            'color'    => '#22c55e',
            'position' => 5,
        ],
        [
            'name'     => 'Seguridad Ciudadana',
            'icon'     => 'shield',
            'color'    => '#0ea5e9',
            'position' => 6,
        ],
        [
            'name'     => 'Turismo',
            'icon'     => 'travel_explore',
            'color'    => '#f97316',
            'position' => 7,
        ],
        [
            'name'     => 'Servicios Municipales',
            'icon'     => 'apartment',
            'color'    => '#64748b',
            'position' => 8,
        ],
        [
            'name'     => 'Gobierno y Transparencia',
            'icon'     => 'gavel',
            'color'    => '#1d4ed8',
            'position' => 9,
        ],
        [
            'name'     => 'Comunicados Oficiales',
            'icon'     => 'campaign',
            'color'    => '#dc2626',
            'position' => 10,
        ],
    ];

    public function run(): void
    {
        $canonicalSlugs = array_map(fn($c) => Str::slug($c['name']), $this->categories);

        // Remove event categories not in the canonical list
        \Illuminate\Support\Facades\DB::table('categories')
            ->where('type', Category::TYPE_EVENT)
            ->whereNotIn('slug', $canonicalSlugs)
            ->delete();

        foreach ($this->categories as $cat) {
            Category::withoutGlobalScopes()->updateOrCreate(
                [
                    'slug' => Str::slug($cat['name']),
                    'type' => Category::TYPE_EVENT,
                ],
                [
                    'name'      => $cat['name'],
                    'slug'      => Str::slug($cat['name']),
                    'type'      => Category::TYPE_EVENT,
                    'icon'      => $cat['icon'],
                    'color'     => $cat['color'],
                    'position'  => $cat['position'],
                    'is_active' => true,
                ],
            );
        }
    }
}
