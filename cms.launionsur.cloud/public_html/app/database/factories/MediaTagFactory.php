<?php

namespace Database\Factories;

use App\Models\MediaTag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MediaTagFactory extends Factory
{
    protected $model = MediaTag::class;

    public function definition()
    {
        $name = $this->faker->unique()->word;
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence,
            'color' => $this->faker->hexColor,
            'created_by' => null,
        ];
    }
}
