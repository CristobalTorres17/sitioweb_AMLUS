<?php

namespace Database\Factories;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->randomNumber(4),
            'content' => $this->faker->paragraphs(5, true),
            'description' => $this->faker->paragraph(),
            'meta_description' => $this->faker->sentence(15),
            'meta_keywords' => implode(',', $this->faker->words(5)),
            'og_image_media_id' => null,
            'is_published' => false,
            'published_at' => null,
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the page is published.
     */
    public function published(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_published' => true,
                'published_at' => now(),
            ];
        });
    }

    /**
     * Indicate that the page is unpublished.
     */
    public function unpublished(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_published' => false,
                'published_at' => null,
            ];
        });
    }

    /**
     * Indicate that the page has an OG image.
     */
    public function withOgImage($mediaId = null): static
    {
        return $this->state(function (array $attributes) use ($mediaId) {
            return [
                'og_image_media_id' => $mediaId,
            ];
        });
    }
}
