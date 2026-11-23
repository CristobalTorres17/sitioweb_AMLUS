<?php

namespace Database\Seeders\Legacy;

use App\Helpers\CharacterEncodingHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegacyVideosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Migrates videos to media table
     */
    public function run(): void
    {
        echo "\n🎬 MIGRATING LEGACY VIDEOS...\n";

        $legacyVideos = DB::connection('legacy')
            ->table('videos')
            ->orderBy('id_video')
            ->get();

        $count = 0;
        $errors = [];

        // Get default user
        $defaultUser = DB::table('users')->where('email', 'admin@alcaldia.com')->first();
        if (!$defaultUser) {
            $defaultUser = DB::table('users')->first();
        }

        foreach ($legacyVideos as $legacy) {
            try {
                $filename = CharacterEncodingHelper::convertCharset($legacy->video);

                // Create media record
                $media = DB::table('media')->insertGetId([
                    'user_id' => $defaultUser->id,
                    'filename' => $filename,
                    'original_filename' => $legacy->video,
                    'path' => 'legacy/videos/' . $filename,
                    'mime_type' => 'video/mp4',
                    'disk' => 'public',
                    'size' => 0,
                    'is_active' => true,
                    'usage_count' => 0,
                    'metadata' => json_encode([
                        'legacy_video_id' => $legacy->id_video,
                        'legacy_album_id' => $legacy->id_album,
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $count++;
                echo "   ✓ Video {$legacy->id_video} → Media {$media}\n";
            } catch (\Exception $e) {
                $errors[] = "Video {$legacy->id_video}: " . $e->getMessage();
            }
        }

        echo "\n📊 SUMMARY\n";
        echo "   Videos migrated: {$count}\n";

        if (!empty($errors)) {
            echo "   Errors: " . count($errors) . "\n";
            foreach (array_slice($errors, 0, 5) as $error) {
                echo "      - {$error}\n";
            }
        }

        echo "\n";
    }
}
