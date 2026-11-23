<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateLegacy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:migrate {phase=all : Phase to run (all, 1, 2, 3, 4, 5, 6, 7, 8, audit, verify)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute legacy database migration to new CMS system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phase = $this->argument('phase');

        $this->info('╔════════════════════════════════════════════════════════════════╗');
        $this->info('║   LEGACY DATABASE MIGRATION — Alcaldía de La Unión            ║');
        $this->info('╚════════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Validate phase
        $validPhases = ['all', '1', '2', '3', '4', '5', '6', '7', '8', 'audit', 'verify'];
        if (!in_array($phase, $validPhases)) {
            $this->error("Invalid phase: $phase");
            $this->info('Valid phases: ' . implode(', ', $validPhases));
            return self::FAILURE;
        }

        try {
            match ($phase) {
                'all' => $this->migrateAll(),
                '1' => $this->migratePhase1(),
                '2' => $this->migratePhase2(),
                '3' => $this->migratePhase3(),
                '4' => $this->migratePhase4(),
                '5' => $this->migratePhase5(),
                '6' => $this->migratePhase6(),
                '7' => $this->migratePhase7(),
                '8' => $this->migratePhase8(),
                'audit' => $this->runAudit(),
                'verify' => $this->verifyMigration(),
            };

            $this->newLine();
            $this->info('╔════════════════════════════════════════════════════════════════╗');
            $this->info('║                    ✅ MIGRATION COMPLETE                       ║');
            $this->info('╚════════════════════════════════════════════════════════════════╝');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Migration failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function migrateAll()
    {
        $this->info('Executing FULL MIGRATION (all 8 phases)...');
        $this->newLine();

        $this->runSeeder('Database\Seeders\Legacy\LegacyMigrationSeeder');
    }

    private function migratePhase1()
    {
        $this->info('▶ Phase 1: Prerequisites (orchestrator only)');
        $this->runSeeder('Database\Seeders\Legacy\LegacyMigrationSeeder');
    }

    private function migratePhase2()
    {
        $this->info('▶ Phase 2: Users & Roles');
        $this->runSeeder('Database\Seeders\Legacy\LegacyRolesSeeder');
        $this->runSeeder('Database\Seeders\Legacy\LegacyUsersSeeder');
    }

    private function migratePhase3()
    {
        $this->info('▶ Phase 3: Content (Articles & Categories)');
        $this->runSeeder('Database\Seeders\Legacy\LegacyCategoriesSeeder');
        $this->runSeeder('Database\Seeders\Legacy\LegacyArticlesSeeder');
    }

    private function migratePhase4()
    {
        $this->info('▶ Phase 4: Events & Galleries');
        $this->runSeeder('Database\Seeders\Legacy\LegacyEventsSeeder');
        $this->runSeeder('Database\Seeders\Legacy\LegacyGalleriesSeeder');
    }

    private function migratePhase5()
    {
        $this->info('▶ Phase 5: Configuration (Banners & Menus)');
        $this->runSeeder('Database\Seeders\Legacy\LegacyBannersSeeder');
        $this->runSeeder('Database\Seeders\Legacy\LegacySettingsSeeder');
        $this->runSeeder('Database\Seeders\Legacy\LegacyMenusSeeder');
    }

    private function migratePhase6()
    {
        $this->info('▶ Phase 6: Multimedia & Galleries');
        $this->runSeeder('Database\Seeders\Legacy\LegacyNivelSeeder');
        $this->runSeeder('Database\Seeders\Legacy\LegacyCarpetaSeeder');
        $this->runSeeder('Database\Seeders\Legacy\LegacyAlbumSeeder');
        $this->runSeeder('Database\Seeders\Legacy\LegacyImagenesSeeder');
        $this->runSeeder('Database\Seeders\Legacy\LegacyVideosSeeder');
    }

    private function migratePhase7()
    {
        $this->info('▶ Phase 7: Documents & Archives');
        $this->runSeeder('Database\Seeders\Legacy\LegacyArchivosSeeder');
    }

    private function migratePhase8()
    {
        $this->info('▶ Phase 8: Audit & History');
        $this->runSeeder('Database\Seeders\Legacy\LegacyHistorialSeeder');
    }

    private function runAudit()
    {
        $this->info('▶ Running Audit Seeders');
        $this->runSeeder('Database\Seeders\Legacy\Phase4EventsAuditSeeder');
        $this->runSeeder('Database\Seeders\Legacy\Phase5SettingsAuditSeeder');
    }

    private function verifyMigration()
    {
        $this->info('▶ Verifying Migration Data');
        $this->newLine();

        $counts = [
            'users' => \DB::table('users')->count(),
            'roles' => \DB::table('roles')->count(),
            'articles' => \DB::table('articles')->count(),
            'categories' => \DB::table('categories')->count(),
            'events' => \DB::table('events')->count(),
            'event_galleries' => \DB::table('event_galleries')->count(),
            'banners' => \DB::table('banners')->count(),
            'menus' => \DB::table('menus')->count(),
            'menu_items' => \DB::table('menu_items')->count(),
            'media' => \DB::table('media')->count(),
            'audit_logs' => \DB::table('audit_logs')->count(),
        ];

        $this->table(
            ['Table', 'Records'],
            collect($counts)->map(fn($count, $table) => [$table, $count])->values()->all()
        );

        $total = array_sum($counts);
        $this->newLine();
        $this->info("Total records: <fg=green>{$total}</>");
    }

    private function runSeeder(string $seederClass)
    {
        $this->info("  → Running: {$seederClass}");

        try {
            Artisan::call('db:seed', ['--class' => $seederClass]);
            $this->info("    <fg=green>✅ Success</>");
        } catch (\Exception $e) {
            $this->error("    ❌ Failed: " . $e->getMessage());
            throw $e;
        }
    }
}
