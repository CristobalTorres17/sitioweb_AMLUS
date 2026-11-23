<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition()
    {
        $title = $this->faker->unique()->sentence(3);
        $kind = $this->faker->randomElement([Service::KIND_SERVICE, Service::KIND_PROCEDURE]);

        return [
            'kind' => $kind,
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->randomNumber(3),
            'description' => $this->faker->paragraph(),
            'category_id' => ServiceCategory::factory(),
            'estimated_time' => $kind === Service::KIND_PROCEDURE
                ? $this->faker->randomElement(['1 día', '2 horas', '3 días', 'Inmediato'])
                : $this->faker->randomElement(['15 min', '1 día', '2 días', null]),
            'cost' => $kind === Service::KIND_PROCEDURE
                ? $this->faker->randomElement(['Gratis', '$10.50', '$3.68'])
                : null,
            'unit' => $this->faker->randomElement([
                'Unidad de Servicios Municipales',
                'Unidad de Bienestar Social',
                'Unidad de Registro Familiar',
            ]),
            'schedule' => 'Lunes a Viernes 8 AM - 4 PM',
            'documentation' => $this->faker->paragraph(),
            'is_active' => $this->faker->boolean(80),
            'order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
