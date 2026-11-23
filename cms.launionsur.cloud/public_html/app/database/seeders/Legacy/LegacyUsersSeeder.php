<?php

namespace Database\Seeders\Legacy;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class LegacyUsersSeeder extends Seeder
{
    /**
     * Migra usuarios desde BD legacy (adm_usuarios) a usuarios nuevos.
     *
     * Mapeo:
     * - adm_usuarios.id → users.id (preservar para FK integrity)
     * - adm_usuarios.usu → users.name
     * - adm_usuarios.mai → users.email (o generar si vacío)
     * - adm_usuarios.pas → users.password (rehashear con bcrypt)
     * - adm_usuarios.{nom,ape,tel,cel,foto} → user_profiles
     * - adm_usuarios.rol → role_user pivot
     *
     * ⚠️ NOTA DE SEGURIDAD:
     * Las contraseñas legacy (MD5) se descartan. Se genera una contraseña temporal segura.
     * Cada usuario DEBE resetear su contraseña al primer login.
     */
    public function run(): void
    {
        echo "\n=== MIGRANDO USUARIOS ===\n";

        $legacyUsers = DB::connection('legacy')
            ->table('adm_usuarios')
            ->get();

        $count = 0;
        $created = 0;
        $failed = 0;

        foreach ($legacyUsers as $legacyUser) {
            try {
                // Generar email único si no existe
                $email = trim($legacyUser->mai);
                if (!$email || $email === '@') {
                    // Usar username + ID para garantizar unicidad
                    $email = Str::slug($legacyUser->usu) . '-' . $legacyUser->id . '@alcaldia.local';
                }
                $email = strtolower($email);

                // Crear usuario con contraseña temporal segura
                $temporaryPassword = bin2hex(random_bytes(16)); // 32 chars random

                $user = User::updateOrCreate(
                    ['id' => $legacyUser->id],
                    [
                        'name' => trim($legacyUser->usu),
                        'email' => $email,
                        'password' => Hash::make($temporaryPassword),
                        'is_active' => true,
                        'role_id' => $legacyUser->rol ?: null, // Rol directo en users
                    ]
                );

                // Crear perfil de usuario con metadata de migración
                $user->profile()->updateOrCreate([], [
                    'phone' => trim($legacyUser->tel) ?: null,
                    'mobile' => trim($legacyUser->cel) ?: null,
                    'bio' => null,
                    'position' => null,
                    'department' => null,
                    'avatar_id' => null, // Migrar en Fase 3
                    'preferences' => json_encode([
                        'legacy_migration' => true,
                        'legacy_id' => $legacyUser->id,
                        'legacy_username' => $legacyUser->usu,
                        'migrated_at' => now()->toDateTimeString(),
                        'must_reset_password' => true,
                    ]),
                ]);

                echo "✓ Usuario ID {$legacyUser->id}: {$legacyUser->usu} ({$email})\n";
                $created++;
            } catch (\Exception $e) {
                echo "✗ Error migrando usuario {$legacyUser->usu}: " . $e->getMessage() . "\n";
                $failed++;
            }

            $count++;
        }

        echo "\n📊 Resumen usuarios:\n";
        echo "  Total procesados: {$count}\n";
        echo "  Creados/actualizados: {$created}\n";
        echo "  Errores: {$failed}\n";
        echo "\n⚠️  IMPORTANTE: Todos los usuarios DEBEN resetear contraseña al primer login.\n";
        echo "   Guarda las contraseñas temporales en lugar seguro o envía emails de reset.\n";
    }
}
