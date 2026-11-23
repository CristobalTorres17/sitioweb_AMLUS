<?php

namespace Database\Factories;

use App\Models\ServiceRequirement;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceRequirementFactory extends Factory
{
    protected $model = ServiceRequirement::class;

    public function definition()
    {
        return [
            'service_id' => Service::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional()->paragraph(),
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}
