<?php

namespace Database\Seeders\Legacy;

use App\Helpers\CharacterEncodingHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LegacyCarpetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Migrates carpeta (folders) to categories table
     */
    public function run(): void
    {
        echo "\n📁 MIGRATING FOLDERS (CARPETA)...\n";

        $legacyCarpetas = DB::connection('legacy')
            ->table('carpeta')
            ->orderBy('id_carpeta')
            ->get();

        $count = 0;
        $errors = [];

        foreach ($legacyCarpetas as $legacy) {
            try {
                // Get the carpeta column - check common names
                $carpetaData = (array)$legacy;
                $carpetaName = null;

                foreach (['carpeta', 'name', 'nombre', 'titulo', 'folder'] as $col) {
                    if (isset($carpetaData[$col]) && !empty($carpetaData[$col])) {
                        $carpetaName = $carpetaData[$col];
                        break;
                    }
                }

                if (!$carpetaName) {
                    // Use any non-empty value
                    foreach ($carpetaData as $col => $val) {
                        if (!in_array($col, ['id_carpeta', 'id']) && !empty($val)) {
                            $carpetaName = $val;
                            break;
                        }
                    }
                }

                if (!$carpetaName) {
                    $errors[] = "Carpeta {$legacy->id_carpeta}: No name found";
                    continue;
                }

                $name = CharacterEncodingHelper::convertCharset($carpetaName);

                // Create or update category
                $slug = Str::slug($name);
                $existing = DB::table('categories')->where('slug', $slug)->first();

                if ($existing) {
                    $category = $existing;
                } else {
                    $categoryId = DB::table('categories')->insertGetId([
                        'name' => $name,
                        'slug' => $slug,
                        'description' => 'Folder: ' . $name,
                        'status' => 'active',
                    ]);
                    $category = (object)['id' => $categoryId];
                }

                $count++;
                echo "   ✓ Carpeta {$legacy->id_carpeta} → Category {$category->id} ({$name})\n";
            } catch (\Exception $e) {
                $errors[] = "Carpeta {$legacy->id_carpeta}: " . $e->getMessage();
            }
        }

        echo "\n📊 SUMMARY\n";
        echo "   Folders migrated: {$count}\n";

        if (!empty($errors)) {
            echo "   Errors: " . count($errors) . "\n";
            foreach (array_slice($errors, 0, 5) as $error) {
                echo "      - {$error}\n";
            }
        }

        echo "\n";
    }
}
