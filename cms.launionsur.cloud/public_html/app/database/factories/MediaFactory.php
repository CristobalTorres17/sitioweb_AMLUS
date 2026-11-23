<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition()
    {
        return [
            'filename' => $this->faker->slug . '.jpg',
            'original_filename' => $this->faker->word . '.jpg',
            'path' => 'media/' . $this->faker->uuid . '.jpg',
            'mime_type' => 'image/jpeg',
            'size' => $this->faker->numberBetween(102400, 5242880), // 100KB to 5MB
            'user_id' => User::inRandomOrder()->first()?->id,
            'is_active' => true,
            'metadata' => [
                'width' => $this->faker->numberBetween(100, 5000),
                'height' => $this->faker->numberBetween(100, 5000),
            ],
        ];
    }

    public function inactive()
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }
}
