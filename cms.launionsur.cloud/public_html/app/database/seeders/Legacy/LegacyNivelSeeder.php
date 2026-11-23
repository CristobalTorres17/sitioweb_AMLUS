<?php

namespace Database\Seeders\Legacy;

use App\Helpers\CharacterEncodingHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LegacyNivelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Migrates nivel (category levels) to categories table
     */
    public function run(): void
    {
        echo "\n📂 MIGRATING CATEGORY LEVELS (NIVEL)...\n";

        $legacyNiveles = DB::connection('legacy')
            ->table('nivel')
            ->orderBy('id_nivel')
            ->get();

        $count = 0;
        $errors = [];

        foreach ($legacyNiveles as $legacy) {
            try {
                // Get the nivel column - it has different names in different rows
                $nivelData = (array)$legacy;
                $nivelName = null;

                // Try common column names
                foreach (['nivel', 'name', 'nombre', 'titulo'] as $col) {
                    if (isset($nivelData[$col]) && !empty($nivelData[$col])) {
                        $nivelName = $nivelData[$col];
                        break;
                    }
                }

                if (!$nivelName) {
                    // Use any non-empty value
                    foreach ($nivelData as $col => $val) {
                        if (!in_array($col, ['id_nivel', 'id']) && !empty($val)) {
                            $nivelName = $val;
                            break;
                        }
                    }
                }

                if (!$nivelName) {
                    $errors[] = "Nivel {$legacy->id_nivel}: No name found";
                    continue;
                }

                $name = CharacterEncodingHelper::convertCharset($nivelName);

                // Create or update category
                $slug = Str::slug($name);
                $existing = DB::table('categories')->where('slug', $slug)->first();

                if ($existing) {
                    $category = $existing;
                } else {
                    $categoryId = DB::table('categories')->insertGetId([
                        'name' => $name,
                        'slug' => $slug,
                        'description' => 'Imported from legacy nivel',
                        'status' => 'active',
                    ]);
                    $category = (object)['id' => $categoryId];
                }

                $count++;
                echo "   ✓ Nivel {$legacy->id_nivel} → Category {$category->id} ({$name})\n";
            } catch (\Exception $e) {
                $errors[] = "Nivel {$legacy->id_nivel}: " . $e->getMessage();
            }
        }

        echo "\n📊 SUMMARY\n";
        echo "   Levels migrated: {$count}\n";

        if (!empty($errors)) {
            echo "   Errors: " . count($errors) . "\n";
            foreach (array_slice($errors, 0, 5) as $error) {
                echo "      - {$error}\n";
            }
        }

        echo "\n";
    }
}
