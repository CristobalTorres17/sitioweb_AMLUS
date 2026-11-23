<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition()
    {
        $title = $this->faker->sentence(6);
        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'excerpt' => $this->faker->paragraph(),
            'content' => $this->faker->paragraphs(5, true),
            'image_path' => 'articles/dummy-' . $this->faker->unique()->numberBetween(1000, 9999) . '.jpg',
            'is_published' => $this->faker->boolean(70),
            'published_at' => now()->subDays($this->faker->numberBetween(0, 30)),
            // 'category_id' will be assigned in seeder
        ];
    }
}
