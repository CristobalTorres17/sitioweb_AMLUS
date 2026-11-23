<?php

namespace Database\Seeders\Legacy;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class LegacyRolesSeeder extends Seeder
{
    /**
     * Migra roles desde BD legacy (adm_rol) a la nueva tabla roles.
     *
     * Mapeo:
     * - adm_rol.rol → roles.id (preservar IDs para FK integrity)
     * - adm_rol.con → roles.name + display_name
     */
    public function run(): void
    {
        echo "\n=== MIGRANDO ROLES ===\n";

        // Obtener roles de BD legacy
        $legacyRoles = DB::connection('legacy')
            ->table('adm_rol')
            ->get();

        $count = 0;
        foreach ($legacyRoles as $legacyRole) {
            try {
                // Crear o actualizar rol en nueva BD
                Role::updateOrCreate(
                    ['id' => $legacyRole->rol],
                    [
                        'name' => $legacyRole->con,
                        'display_name' => $legacyRole->con,
                    ]
                );

                echo "✓ Rol ID {$legacyRole->rol}: {$legacyRole->con}\n";
                $count++;
            } catch (\Exception $e) {
                echo "✗ Error migrand rol {$legacyRole->rol}: " . $e->getMessage() . "\n";
            }
        }

        echo "\n✅ Total roles migrados: {$count}\n";
    }
}
