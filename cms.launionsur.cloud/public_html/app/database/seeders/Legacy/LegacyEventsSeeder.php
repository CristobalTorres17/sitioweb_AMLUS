<?php

namespace Database\Seeders\Legacy;

use App\Helpers\CharacterEncodingHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LegacyEventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n🎯 MIGRATING EVENTS...\n";

        $legacyEvents = DB::connection('legacy')
            ->table('eventos')
            ->where('eliminado', 0) // Only active events
            ->get();

        $count = 0;
        $errors = [];

        foreach ($legacyEvents as $legacy) {
            try {
                // Get author by legacy ID from user_profiles.preferences
                $profile = DB::table('user_profiles')
                    ->whereJsonContains('preferences->legacy_id', $legacy->id_usu)
                    ->first();

                $author = null;
                if ($profile) {
                    $author = DB::table('users')->where('id', $profile->user_id)->first();
                }

                if (!$author) {
                    // Fallback to admin user
                    $admin = DB::table('users')->where('email', 'admin@alcaldia.com')->first();
                    if (!$admin) {
                        $admin = DB::table('users')->whereRaw('email LIKE ?', ['admin%'])->first();
                    }
                    if (!$admin) {
                        $errors[] = "Event {$legacy->id_evento}: No admin user found";
                        continue;
                    }
                    $author = $admin;
                }

                // Convert charset
                $title = CharacterEncodingHelper::convertCharset($legacy->nombre);
                $description = CharacterEncodingHelper::convertCharset($legacy->objetivo);

                // Create event
                $event = DB::table('events')->insertGetId([
                    'title' => $title,
                    'slug' => Str::slug($title) . '-' . uniqid(),
                    'description' => $description,
                    'content' => null,
                    'event_date' => $legacy->fecha . ' 00:00:00',
                    'event_date_end' => null,
                    'location' => null,
                    'organizer' => 'Alcaldía de La Unión',
                    'featured_image_id' => null,
                    'status' => $legacy->estado === 'Activo' ? 'published' : 'draft',
                    'published_at' => $legacy->estado === 'Activo' ? now() : null,
                    'author_id' => $author->id,
                    'meta_title' => $title,
                    'meta_description' => substr($description, 0, 160),
                    'is_featured' => false,
                    'metadata' => json_encode([
                        'legacy_event_id' => $legacy->id_evento,
                        'legacy_event_type' => $legacy->tipo,
                        'legacy_correlativo' => $legacy->correlativo,
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $count++;
                echo "   ✓ Event {$legacy->id_evento} → ID {$event}\n";
            } catch (\Exception $e) {
                $errors[] = "Event {$legacy->id_evento}: " . $e->getMessage();
            }
        }

        echo "\n📊 SUMMARY\n";
        echo "   Events migrated: {$count}\n";

        if (!empty($errors)) {
            echo "   Errors: " . count($errors) . "\n";
            foreach (array_slice($errors, 0, 5) as $error) {
                echo "      - {$error}\n";
            }
        }

        echo "\n";
    }
}
