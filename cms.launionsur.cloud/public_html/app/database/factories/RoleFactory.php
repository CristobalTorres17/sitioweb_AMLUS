<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = ['super_admin', 'editor', 'reviewer', 'viewer'];

        return [
            'name' => $this->faker->unique()->randomElement($roles),
            'description' => $this->faker->sentence(),
        ];
    }
}
