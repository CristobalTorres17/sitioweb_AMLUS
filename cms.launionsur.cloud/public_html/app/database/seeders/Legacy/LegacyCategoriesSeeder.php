<?php

namespace Database\Seeders\Legacy;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Category;

class LegacyCategoriesSeeder extends Seeder
{
    /**
     * Migra categorías de noticias desde BD legacy.
     *
     * Mapeo:
     * - catenoti.id_catenoti → categories.id
     * - catenoti.catenoti → categories.name + slug
     */
    public function run(): void
    {
        echo "\n=== MIGRANDO CATEGORÍAS ===\n";

        $legacyCategories = DB::connection('legacy')
            ->table('catenoti')
            ->get();

        $count = 0;
        foreach ($legacyCategories as $legacyCat) {
            try {
                $slug = Str::slug($legacyCat->catenoti);

                Category::updateOrCreate(
                    ['id' => $legacyCat->id_catenoti],
                    [
                        'name' => $legacyCat->catenoti,
                        'slug' => $slug,
                    ]
                );

                echo "✓ Categoría ID {$legacyCat->id_catenoti}: {$legacyCat->catenoti}\n";
                $count++;
            } catch (\Exception $e) {
                echo "✗ Error migrando categoría {$legacyCat->catenoti}: " . $e->getMessage() . "\n";
            }
        }

        echo "\n✅ Total categorías migradas: {$count}\n";
    }
}
