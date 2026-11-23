<?php

namespace Database\Seeders\Legacy;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Phase5SettingsAuditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n=== PHASE 5: LEGACY SETTINGS & MENUS AUDIT ===\n";

        // Audit banner table
        $banners = DB::connection('legacy')->table('banner')->get();
        echo "\n🎨 BANNER TABLE\n";
        echo "   Total records: " . count($banners) . "\n";
        if (count($banners) > 0) {
            echo "   Columns: " . implode(', ', array_keys((array) $banners[0])) . "\n";
            echo "   Sample record:\n";
            $first = $banners[0];
            foreach ((array) $first as $col => $val) {
                echo "      - {$col}: " . substr($val, 0, 60) . "\n";
            }
        }

        // Audit historia, mision, vision, valores
        $historia = DB::connection('legacy')->table('historia')->get();
        echo "\n📄 HISTORIA TABLE\n";
        echo "   Total records: " . count($historia) . "\n";
        if (count($historia) > 0) {
            echo "   Columns: " . implode(', ', array_keys((array) $historia[0])) . "\n";
        }

        $mision = DB::connection('legacy')->table('mision')->get();
        echo "\n📄 MISION TABLE\n";
        echo "   Total records: " . count($mision) . "\n";
        if (count($mision) > 0) {
            echo "   Columns: " . implode(', ', array_keys((array) $mision[0])) . "\n";
        }

        $vision = DB::connection('legacy')->table('vision')->get();
        echo "\n📄 VISION TABLE\n";
        echo "   Total records: " . count($vision) . "\n";
        if (count($vision) > 0) {
            echo "   Columns: " . implode(', ', array_keys((array) $vision[0])) . "\n";
        }

        $valores = DB::connection('legacy')->table('valores')->get();
        echo "\n📄 VALORES TABLE\n";
        echo "   Total records: " . count($valores) . "\n";
        if (count($valores) > 0) {
            echo "   Columns: " . implode(', ', array_keys((array) $valores[0])) . "\n";
        }

        // Audit menu
        $menu = DB::connection('legacy')->table('menu')->get();
        echo "\n🔗 MENU TABLE\n";
        echo "   Total records: " . count($menu) . "\n";
        if (count($menu) > 0) {
            echo "   Columns: " . implode(', ', array_keys((array) $menu[0])) . "\n";
            echo "   Sample record:\n";
            $first = $menu[0];
            foreach ((array) $first as $col => $val) {
                echo "      - {$col}: " . substr($val, 0, 60) . "\n";
            }
        }

        // Audit submenu
        $submenu = DB::connection('legacy')->table('submenu')->get();
        echo "\n🔗 SUBMENU TABLE\n";
        echo "   Total records: " . count($submenu) . "\n";
        if (count($submenu) > 0) {
            echo "   Columns: " . implode(', ', array_keys((array) $submenu[0])) . "\n";
        }

        echo "\n✅ AUDIT COMPLETE\n\n";
    }
}
