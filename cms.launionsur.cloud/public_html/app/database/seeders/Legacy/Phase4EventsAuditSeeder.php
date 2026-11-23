<?php

namespace Database\Seeders\Legacy;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Phase4EventsAuditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n=== PHASE 4: LEGACY EVENTS & GALLERIES AUDIT ===\n";

        // Audit eventos table
        $eventos = DB::connection('legacy')->table('eventos')->get();
        echo "\n📊 EVENTOS TABLE\n";
        echo "   Total records: " . count($eventos) . "\n";
        if (count($eventos) > 0) {
            echo "   Columns: " . implode(', ', array_keys((array) $eventos[0])) . "\n";
            echo "   Sample record:\n";
            $first = $eventos[0];
            foreach ((array) $first as $col => $val) {
                echo "      - {$col}: " . substr($val, 0, 60) . "\n";
            }
        }

        // Audit galeria table
        $galeria = DB::connection('legacy')->table('galeria')->get();
        echo "\n📸 GALERIA TABLE\n";
        echo "   Total records: " . count($galeria) . "\n";
        if (count($galeria) > 0) {
            echo "   Columns: " . implode(', ', array_keys((array) $galeria[0])) . "\n";
            echo "   Sample record:\n";
            $first = $galeria[0];
            foreach ((array) $first as $col => $val) {
                echo "      - {$col}: " . substr($val, 0, 60) . "\n";
            }
        }

        // Check for any image references
        echo "\n🖼️  IMAGE REFERENCES\n";
        $eventosWithImages = DB::connection('legacy')
            ->table('eventos')
            ->whereNotNull('imagen')
            ->count();
        echo "   Eventos with imagen: " . $eventosWithImages . "\n";

        $galeriaWithImages = DB::connection('legacy')
            ->table('galeria')
            ->whereNotNull('imagen')
            ->count();
        echo "   Galeria with imagen: " . $galeriaWithImages . "\n";

        echo "\n✅ AUDIT COMPLETE\n\n";
    }
}
