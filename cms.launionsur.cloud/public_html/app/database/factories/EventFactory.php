<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(5);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'description' => $this->faker->paragraph(),
            'content' => $this->faker->paragraphs(4, true),
            'event_date' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
            'event_date_end' => $this->faker->boolean(40) ? $this->faker->dateTimeBetween('+1 week', '+4 months') : null,
            'location' => $this->faker->city(),
            'organizer' => $this->faker->company(),
            'featured_image_id' => null,
            'status' => $this->faker->randomElement(['draft', 'published', 'archived']),
            'published_at' => now(),
            'author_id' => User::factory(),
            'meta_title' => $title,
            'meta_description' => $this->faker->sentence(),
            'is_featured' => $this->faker->boolean(20),
            'metadata' => [
                'source' => 'factory',
            ],
        ];
    }
}
