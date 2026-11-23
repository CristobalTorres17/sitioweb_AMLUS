<?php

namespace Database\Seeders\Legacy;

use App\Helpers\CharacterEncodingHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LegacyBannersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n🎨 MIGRATING BANNERS...\n";

        $legacyBanners = DB::connection('legacy')
            ->table('banner')
            ->get();

        $count = 0;
        $errors = [];

        foreach ($legacyBanners as $legacy) {
            try {
                $title = CharacterEncodingHelper::convertCharset($legacy->detalle) ?: 'Banner ' . $legacy->id_banner;
                $imageName = CharacterEncodingHelper::convertCharset($legacy->imagen);

                // Create banner with image path
                DB::table('banners')->insert([
                    'title' => $title ?: 'Banner ' . $legacy->id_banner,
                    'subtitle' => null,
                    'image_path' => 'legacy/banners/' . $imageName,
                    'cta_text' => null,
                    'cta_url' => null,
                    'show_button' => false,
                    'page' => 'home',
                    'sort_order' => $legacy->id_banner,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $count++;
                echo "   ✓ Banner {$legacy->id_banner}: {$title}\n";
            } catch (\Exception $e) {
                $errors[] = "Banner {$legacy->id_banner}: " . $e->getMessage();
            }
        }

        echo "\n📊 SUMMARY\n";
        echo "   Banners migrated: {$count}\n";

        if (!empty($errors)) {
            echo "   Errors: " . count($errors) . "\n";
            foreach (array_slice($errors, 0, 5) as $error) {
                echo "      - {$error}\n";
            }
        }

        echo "\n";
    }
}
