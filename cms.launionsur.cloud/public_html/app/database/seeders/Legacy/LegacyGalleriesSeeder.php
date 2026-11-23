<?php

namespace Database\Seeders\Legacy;

use App\Helpers\CharacterEncodingHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LegacyGalleriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n📸 MIGRATING EVENT GALLERIES...\n";

        $legacyGalleries = DB::connection('legacy')
            ->table('galeria')
            ->where('elimindo', 0) // Only active galleries
            ->orderBy('id_galeria')
            ->get();

        $count = 0;
        $orphaned = 0;
        $errors = [];
        $skipped = [];

        foreach ($legacyGalleries as $legacy) {
            try {
                // Get event by legacy ID
                $event = DB::table('events')
                    ->whereJsonContains('metadata->legacy_event_id', (int) $legacy->id_evento)
                    ->first();

                if (!$event) {
                    // Check if the legacy event exists but wasn't migrated (deleted/inactive)
                    $legacyEvent = DB::connection('legacy')
                        ->table('eventos')
                        ->where('id_evento', (int) $legacy->id_evento)
                        ->first();

                    if ($legacyEvent && $legacyEvent->eliminado == 1) {
                        // Event was deleted - try to link to a default event or create orphaned media
                        $fallbackEvent = DB::table('events')->first(); // Use first available event as fallback

                        if ($fallbackEvent) {
                            echo "   ⚠️  Gallery {$legacy->id_galeria} (orphaned - Event {$legacy->id_evento} deleted)\n";

                            $imageName = CharacterEncodingHelper::convertCharset($legacy->imagen);

                            // Create media record without event link
                            $media = DB::table('media')->insertGetId([
                                'user_id' => $fallbackEvent->author_id,
                                'filename' => $imageName,
                                'path' => 'legacy/galleries/' . $imageName,
                                'mime_type' => 'image/jpeg',
                                'size' => 0,
                                'metadata' => json_encode([
                                    'legacy_gallery_id' => $legacy->id_galeria,
                                    'legacy_event_id' => $legacy->id_evento,
                                    'orphaned_reason' => 'Referenced event was deleted',
                                    'original_filename' => $legacy->imagen,
                                ]),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);

                            $orphaned++;
                            echo "      → Stored as Media {$media} (orphaned)\n";
                        } else {
                            $skipped[] = "Gallery {$legacy->id_galeria}: No default event available for orphaned gallery";
                        }
                    } else {
                        $skipped[] = "Gallery {$legacy->id_galeria}: Event {$legacy->id_evento} not found";
                    }
                    continue;
                }

                // Create media record for the image
                $imageName = CharacterEncodingHelper::convertCharset($legacy->imagen);

                // Media path in legacy server
                $media = DB::table('media')->insertGetId([
                    'user_id' => $event->author_id,
                    'filename' => $imageName,
                    'path' => 'legacy/galleries/' . $imageName,
                    'mime_type' => 'image/jpeg', // Assume JPEG for legacy images
                    'size' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create event_galleries relationship
                DB::table('event_galleries')->insert([
                    'event_id' => $event->id,
                    'media_id' => $media,
                    'position' => 0,
                    'caption' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $count++;
                echo "   ✓ Gallery {$legacy->id_galeria} → Media {$media} → Event {$event->id}\n";
            } catch (\Exception $e) {
                $errors[] = "Gallery {$legacy->id_galeria}: " . $e->getMessage();
            }
        }

        echo "\n📊 SUMMARY\n";
        echo "   Galleries migrated: {$count}\n";
        echo "   Orphaned galleries: {$orphaned}\n";
        echo "   Skipped: " . count($skipped) . "\n";

        if (!empty($errors)) {
            echo "   Errors: " . count($errors) . "\n";
            foreach (array_slice($errors, 0, 5) as $error) {
                echo "      - {$error}\n";
            }
        }

        if (!empty($skipped)) {
            echo "   Skipped reasons:\n";
            foreach (array_slice($skipped, 0, 5) as $reason) {
                echo "      - {$reason}\n";
            }
        }

        echo "\n";
    }
}
