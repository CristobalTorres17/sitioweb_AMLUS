<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Banner>
 */
class BannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'subtitle' => fake()->sentence(),
            'cta_text' => fake()->word(),
            'cta_url' => fake()->url(),
            'show_button' => fake()->boolean(),
            'page' => fake()->randomElement(['home', 'services', 'articles', 'about']),
            'sort_order' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the banner should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the banner is for the home page.
     */
    public function forHome(): static
    {
        return $this->state(fn(array $attributes) => [
            'page' => 'home',
        ]);
    }

    /**
     * Indicate that the banner is for a specific page.
     */
    public function forPage(string $page): static
    {
        return $this->state(fn(array $attributes) => [
            'page' => $page,
        ]);
    }
}
