<?php

namespace Database\Seeders\Legacy;

use App\Helpers\CharacterEncodingHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegacyImagenesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Migrates imagenes (photos) to media table
     */
    public function run(): void
    {
        echo "\n🖼️  MIGRATING LEGACY IMAGES...\n";

        $legacyImages = DB::connection('legacy')
            ->table('imagenes')
            ->orderBy('id_imagen')
            ->get();

        $count = 0;
        $errors = [];

        // Get default user
        $defaultUser = DB::table('users')->where('email', 'admin@alcaldia.com')->first();
        if (!$defaultUser) {
            $defaultUser = DB::table('users')->first();
        }

        foreach ($legacyImages as $legacy) {
            try {
                $filename = CharacterEncodingHelper::convertCharset($legacy->imagen);

                // Create media record
                $media = DB::table('media')->insertGetId([
                    'user_id' => $defaultUser->id,
                    'filename' => $filename,
                    'original_filename' => $legacy->imagen,
                    'path' => 'legacy/images/' . $filename,
                    'mime_type' => 'image/jpeg',
                    'disk' => 'public',
                    'size' => 0,
                    'is_active' => true,
                    'usage_count' => 0,
                    'metadata' => json_encode([
                        'legacy_image_id' => $legacy->id_imagen,
                        'legacy_album_id' => $legacy->id_album,
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $count++;
                echo "   ✓ Image {$legacy->id_imagen} → Media {$media} (Album {$legacy->id_album})\n";
            } catch (\Exception $e) {
                $errors[] = "Image {$legacy->id_imagen}: " . $e->getMessage();
            }
        }

        echo "\n📊 SUMMARY\n";
        echo "   Images migrated: {$count}\n";

        if (!empty($errors)) {
            echo "   Errors: " . count($errors) . "\n";
            foreach (array_slice($errors, 0, 5) as $error) {
                echo "      - {$error}\n";
            }
        }

        echo "\n";
    }
}
