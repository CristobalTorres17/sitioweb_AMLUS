<?php

namespace Database\Seeders\Legacy;

use App\Helpers\CharacterEncodingHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LegacyAlbumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Migrates photo albums to categories (as media organization)
     */
    public function run(): void
    {
        echo "\n📁 MIGRATING PHOTO ALBUMS...\n";

        $legacyAlbums = DB::connection('legacy')
            ->table('album')
            ->orderBy('id_album')
            ->get();

        $count = 0;
        $errors = [];

        foreach ($legacyAlbums as $legacy) {
            try {
                $name = CharacterEncodingHelper::convertCharset($legacy->album);

                // Create or update category
                $slug = Str::slug($name);
                $existing = DB::table('categories')->where('slug', $slug)->first();

                if ($existing) {
                    $category = $existing;
                } else {
                    $categoryId = DB::table('categories')->insertGetId([
                        'name' => $name,
                        'slug' => $slug,
                        'description' => 'Photo Album: ' . $name,
                        'status' => 'active',
                    ]);
                    $category = (object)['id' => $categoryId];
                }

                // Link any media with this album to the category via metadata
                // (This is informational since media table doesn't have category_id FK)

                $count++;
                echo "   ✓ Album {$legacy->id_album} → Category {$category->id} ({$name})\n";
            } catch (\Exception $e) {
                $errors[] = "Album {$legacy->id_album}: " . $e->getMessage();
            }
        }

        echo "\n📊 SUMMARY\n";
        echo "   Albums migrated: {$count}\n";

        if (!empty($errors)) {
            echo "   Errors: " . count($errors) . "\n";
            foreach (array_slice($errors, 0, 5) as $error) {
                echo "      - {$error}\n";
            }
        }

        echo "\n";
    }
}
