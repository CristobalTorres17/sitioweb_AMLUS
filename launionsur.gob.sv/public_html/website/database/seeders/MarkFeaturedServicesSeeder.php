<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarkFeaturedServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mark first 4 services as featured (type = service)
        $services = DB::table('services')
            ->join('categories', 'services.category_id', '=', 'categories.id')
            ->where('services.is_active', 1)
            ->where('categories.type', 'service')
            ->orderBy('services.created_at', 'desc')
            ->limit(4)
            ->pluck('services.id');

        if ($services->isNotEmpty()) {
            foreach ($services as $index => $serviceId) {
                DB::table('services')
                    ->where('id', $serviceId)
                    ->update([
                        'is_featured' => true,
                        'featured_order' => $index + 1,
                    ]);
            }
            echo "✓ Marked " . count($services) . " services as featured\n";
        }

        // Mark first 4 procedures/tramites as featured (type = procedure)
        $tramites = DB::table('services')
            ->join('categories', 'services.category_id', '=', 'categories.id')
            ->where('services.is_active', 1)
            ->where('categories.type', 'procedure')
            ->orderBy('services.created_at', 'desc')
            ->limit(4)
            ->pluck('services.id');

        if ($tramites->isNotEmpty()) {
            foreach ($tramites as $index => $tramiteId) {
                DB::table('services')
                    ->where('id', $tramiteId)
                    ->update([
                        'is_featured' => true,
                        'featured_order' => $index + 1,
                    ]);
            }
            echo "✓ Marked " . count($tramites) . " procedures as featured\n";
        }
    }
}
