<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        // Extracted from legacy pages:
        // - apps/alcaldia-page/services.php      => Servicios
        // - apps/alcaldia-page/short-circuit-repair.php => Trámites
        $categoriesByType = [
            ServiceCategory::TYPE_SERVICE => [
                'Salud',
                'Infraestructura',
                'Asistencia Social',
            ],
            ServiceCategory::TYPE_TRAMITE => [
                'Trámites Legales',
                'Constancias',
                'Trámites Empresariales',
                'Registro Familiar',
                'Inmuebles',
                'Trámites Cementerio',
                'Trámites de Construcción',
                'Contravencional',
                'Mercado',
                'Visto Bueno',
            ],
        ];

        $position = 1;

        foreach ($categoriesByType as $type => $categories) {
            foreach ($categories as $name) {
                ServiceCategory::firstOrCreate(
                    [
                        'slug' => Str::slug($name),
                        'type' => $type,
                    ],
                    [
                        'name' => $name,
                        'slug' => Str::slug($name),
                        'type' => $type,
                        'is_active' => true,
                        'position' => $position++,
                    ],
                );
            }
        }
    }
}
