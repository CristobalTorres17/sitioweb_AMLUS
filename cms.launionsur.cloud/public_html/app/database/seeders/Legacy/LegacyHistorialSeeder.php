<?php

namespace Database\Seeders\Legacy;

use App\Helpers\CharacterEncodingHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegacyHistorialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Migrates historial (audit trail) to audit_logs table
     */
    public function run(): void
    {
        echo "\n📋 MIGRATING LEGACY AUDIT TRAIL...\n";

        $legacyHistorial = DB::connection('legacy')
            ->table('historial')
            ->orderBy('id_historial')
            ->get();

        $count = 0;
        $errors = [];

        foreach ($legacyHistorial as $legacy) {
            try {
                $accion = CharacterEncodingHelper::convertCharset($legacy->accion);
                $lugar = CharacterEncodingHelper::convertCharset($legacy->lugar);
                $usuario = $legacy->usu ?? 'Unknown';

                // Try to find user - simple lookup
                $user = null;
                if (is_numeric($usuario)) {
                    $user = DB::table('users')->where('id', (int)$usuario)->first();
                }

                $userId = $user?->id;

                $auditEntry = DB::table('audit_logs')->insertGetId([
                    'user_id' => $userId,
                    'action' => $accion,
                    'model_type' => 'LegacyRecord',
                    'model_id' => $legacy->id_lugar,
                    'ip_address' => null,
                    'old_values' => null,
                    'new_values' => json_encode([
                        'legacy_historial_id' => $legacy->id_historial,
                        'legacy_user' => $usuario,
                        'lugar' => $lugar,
                        'estado' => $legacy->estado,
                        'imported_from_legacy' => true,
                    ]),
                    'is_sensitive' => false,
                    'is_restoreable' => false,
                    'reason' => 'Imported from legacy historial',
                    'created_at' => \Carbon\Carbon::parse($legacy->fecha)->startOfDay(),
                ]);

                $count++;
                echo "   ✓ Historial {$legacy->id_historial} → Audit log {$auditEntry}\n";
            } catch (\Exception $e) {
                $errors[] = "Historial {$legacy->id_historial}: " . $e->getMessage();
            }
        }

        echo "\n📊 SUMMARY\n";
        echo "   Audit entries migrated: {$count}\n";

        if (!empty($errors)) {
            echo "   Errors: " . count($errors) . "\n";
            foreach (array_slice($errors, 0, 5) as $error) {
                echo "      - {$error}\n";
            }
        }

        echo "\n";
    }
}
