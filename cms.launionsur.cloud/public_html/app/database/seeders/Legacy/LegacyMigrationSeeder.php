<?php

namespace Database\Seeders\Legacy;

use Illuminate\Database\Seeder;

class LegacyMigrationSeeder extends Seeder
{
    /**
     * Orchestrador principal para migración de datos legacy.
     *
     * Ejecuta seeders en orden específico respetando FK constraints:
     * 1. Roles (sin dependencias)
     * 2. Usuarios (depende de roles)
     * 3. [Fase 3] Categorías
     * 4. [Fase 3] Artículos
     * ... etc
     */
    public function run(): void
    {
        echo "\n";
        echo "╔═══════════════════════════════════════════════════════════════╗\n";
        echo "║         INICIANDO MIGRACIÓN LEGACY → CMS NUEVO (2026)         ║\n";
        echo "╚═══════════════════════════════════════════════════════════════╝\n";

        echo "\n▶ FASE 2: USUARIOS Y ROLES\n";
        echo "  └─ Migrando estructura de acceso...\n";

        // Fase 2.1 — Roles
        $this->call(LegacyRolesSeeder::class);

        // Fase 2.2 — Usuarios
        $this->call(LegacyUsersSeeder::class);

        echo "\n▶ FASE 3: CONTENIDO PRINCIPAL\n";
        echo "  └─ Migrando categorías y artículos...\n";

        // Fase 3.1 — Categorías
        $this->call(LegacyCategoriesSeeder::class);

        // Fase 3.2 — Artículos
        $this->call(LegacyArticlesSeeder::class);

        echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
        echo "║                   ✅ FASE 3 COMPLETADA                        ║\n";
        echo "╚═══════════════════════════════════════════════════════════════╝\n";

        echo "\n▶ FASE 4: EVENTOS Y GALERÍAS\n";
        echo "  └─ Migrando eventos y multimedia asociada...\n";

        // Fase 4.1 — Eventos
        $this->call(LegacyEventsSeeder::class);

        // Fase 4.2 — Galerías de eventos
        $this->call(LegacyGalleriesSeeder::class);

        echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
        echo "║                   ✅ FASE 4 COMPLETADA                        ║\n";
        echo "╚═══════════════════════════════════════════════════════════════╝\n";

        echo "\n▶ FASE 5: CONFIGURACIÓN DE SITIO (BANNERS Y MENÚS)\n";
        echo "  └─ Migrando banners y estructura de navegación...\n";

        // Fase 5.1 — Banners
        $this->call(LegacyBannersSeeder::class);

        // Fase 5.2 — Settings
        $this->call(LegacySettingsSeeder::class);

        // Fase 5.3 — Menus
        $this->call(LegacyMenusSeeder::class);

        echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
        echo "║                   ✅ FASE 5 COMPLETADA                        ║\n";
        echo "╚═══════════════════════════════════════════════════════════════╝\n";

        echo "\n▶ FASE 6: MULTIMEDIA Y GALERÍAS\n";
        echo "  └─ Migrando imágenes, videos y álbumes...\n";

        // Fase 6.1 — Nivel (category levels for organization)
        $this->call(LegacyNivelSeeder::class);

        // Fase 6.2 — Carpeta (folders for organization)
        $this->call(LegacyCarpetaSeeder::class);

        // Fase 6.3 — Álbumes
        $this->call(LegacyAlbumSeeder::class);

        // Fase 6.4 — Imágenes
        $this->call(LegacyImagenesSeeder::class);

        // Fase 6.5 — Videos
        $this->call(LegacyVideosSeeder::class);

        echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
        echo "║                   ✅ FASE 6 COMPLETADA                        ║\n";
        echo "╚═══════════════════════════════════════════════════════════════╝\n";

        echo "\n▶ FASE 7: DOCUMENTOS Y ARCHIVOS\n";
        echo "  └─ Migrando archivos y documentos...\n";

        // Fase 7.1 — Archivos/Documentos
        $this->call(LegacyArchivosSeeder::class);

        echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
        echo "║                   ✅ FASE 7 COMPLETADA                        ║\n";
        echo "╚═══════════════════════════════════════════════════════════════╝\n";

        echo "\n▶ FASE 8: AUDITORÍA Y REGISTRO HISTÓRICO\n";
        echo "  └─ Migrando historial y registros de auditoría...\n";

        // Fase 8.1 — Historial (audit trail)
        $this->call(LegacyHistorialSeeder::class);

        echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
        echo "║                   ✅ FASE 8 COMPLETADA                        ║\n";
        echo "╚═══════════════════════════════════════════════════════════════╝\n";

        echo "\n📌 Próximos pasos:\n";
        echo "   1. Verificar media: SELECT COUNT(*) FROM media;\n";
        echo "   2. Verificar categorías: SELECT COUNT(*) FROM categories;\n";
        echo "   3. Verificar audit_logs: SELECT COUNT(*) FROM audit_logs;\n";
        echo "   4. Revisar rutas heredadas (legacy URLs)\n";
        echo "   5. Crear redirecciones 301 si es necesario\n";
    }
}
