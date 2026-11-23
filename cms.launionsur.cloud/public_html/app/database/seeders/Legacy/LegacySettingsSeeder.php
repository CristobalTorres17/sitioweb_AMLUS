<?php

namespace Database\Seeders\Legacy;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegacySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Migrates static content pages: Historia, Misión, Visión, Valores
     * Note: These tables are empty in legacy DB, but seeder is created for future use
     */
    public function run(): void
    {
        echo "\n📄 MIGRATING SETTINGS/PAGES...\n";

        $historia = DB::connection('legacy')->table('historia')->get();
        $mision = DB::connection('legacy')->table('mision')->get();
        $vision = DB::connection('legacy')->table('vision')->get();
        $valores = DB::connection('legacy')->table('valores')->get();

        $count = 0;

        // Migrate Historia
        if (count($historia) > 0) {
            foreach ($historia as $legacy) {
                // Implementation would go here
                $count++;
            }
        }

        // Migrate Misión
        if (count($mision) > 0) {
            foreach ($mision as $legacy) {
                // Implementation would go here
                $count++;
            }
        }

        // Migrate Visión
        if (count($vision) > 0) {
            foreach ($vision as $legacy) {
                // Implementation would go here
                $count++;
            }
        }

        // Migrate Valores
        if (count($valores) > 0) {
            foreach ($valores as $legacy) {
                // Implementation would go here
                $count++;
            }
        }

        echo "\n📊 SUMMARY\n";
        echo "   Settings/Pages migrated: {$count}\n";
        echo "   ⚠️  Note: Legacy tables (historia, mision, vision, valores) are empty\n";
        echo "   Create these pages manually in CMS or populate from another source.\n";
        echo "\n";
    }
}
