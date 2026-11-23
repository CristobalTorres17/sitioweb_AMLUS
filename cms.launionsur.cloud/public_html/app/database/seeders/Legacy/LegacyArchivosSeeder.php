<?php

namespace Database\Seeders\Legacy;

use App\Helpers\CharacterEncodingHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegacyArchivosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Migrates archivos (documents) to media table
     */
    public function run(): void
    {
        echo "\n📄 MIGRATING LEGACY DOCUMENTS/ARCHIVES...\n";

        $legacyArchivos = DB::connection('legacy')
            ->table('archivos')
            ->orderBy('id_archivo')
            ->get();

        $count = 0;
        $errors = [];

        // Get default user
        $defaultUser = DB::table('users')->where('email', 'admin@alcaldia.com')->first();
        if (!$defaultUser) {
            $defaultUser = DB::table('users')->first();
        }

        foreach ($legacyArchivos as $legacy) {
            try {
                $filename = CharacterEncodingHelper::convertCharset($legacy->archivo);
                $nombre = CharacterEncodingHelper::convertCharset($legacy->nombre);
                $ruta = CharacterEncodingHelper::convertCharset($legacy->ruta);
                $archivoOriginal = CharacterEncodingHelper::convertCharset($legacy->archivo);

                // Determine MIME type
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $mimeTypes = [
                    'pdf' => 'application/pdf',
                    'doc' => 'application/msword',
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'xls' => 'application/vnd.ms-excel',
                    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'ppt' => 'application/vnd.ms-powerpoint',
                    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'txt' => 'text/plain',
                    'zip' => 'application/zip',
                    'rar' => 'application/x-rar-compressed',
                    'php' => 'application/octet-stream',
                ];
                $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';

                // Create media record
                $media = DB::table('media')->insertGetId([
                    'user_id' => $defaultUser->id,
                    'filename' => $filename,
                    'original_filename' => 'legacy_archivo_' . $legacy->id_archivo . '.' . $ext, // Use generated name to avoid charset issues
                    'path' => $ruta . $filename,
                    'mime_type' => $mimeType,
                    'disk' => 'public',
                    'size' => 0,
                    'is_active' => true,
                    'usage_count' => 0,
                    'metadata' => json_encode([
                        'legacy_archivo_id' => $legacy->id_archivo,
                        'legacy_menu_id' => $legacy->id_menu,
                        'legacy_submenu_id' => $legacy->id_submenu,
                        'legacy_carpeta_id' => $legacy->id_carpeta,
                        'legacy_nivel_id' => $legacy->id_nivel,
                        'legacy_subnivel_id' => $legacy->id_subnivel,
                        'legacy_anno_id' => $legacy->id_anno,
                        'title' => $nombre,
                        'description' => $legacy->anexo ? CharacterEncodingHelper::convertCharset($legacy->anexo) : null,
                        'original_path' => $ruta,
                        'original_filename_legacy' => $archivoOriginal,
                    ]),
                    'created_at' => $legacy->fechaPublicacion ? \Carbon\Carbon::parse($legacy->fechaPublicacion) : now(),
                    'updated_at' => $legacy->fechaPublicacion ? \Carbon\Carbon::parse($legacy->fechaPublicacion) : now(),
                ]);

                $count++;
                echo "   ✓ Archivo {$legacy->id_archivo} → Media {$media} ({$nombre})\n";
            } catch (\Exception $e) {
                $errors[] = "Archivo {$legacy->id_archivo}: " . $e->getMessage();
            }
        }

        echo "\n📊 SUMMARY\n";
        echo "   Documents migrated: {$count}\n";

        if (!empty($errors)) {
            echo "   Errors: " . count($errors) . "\n";
            foreach (array_slice($errors, 0, 5) as $error) {
                echo "      - {$error}\n";
            }
        }

        echo "\n";
    }
}
