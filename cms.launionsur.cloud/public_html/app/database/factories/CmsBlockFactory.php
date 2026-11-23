<?php

namespace Database\Factories;

use App\Models\CmsBlock;
use Illuminate\Database\Eloquent\Factories\Factory;

class CmsBlockFactory extends Factory
{
    protected $model = CmsBlock::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'slug' => $this->faker->unique()->slug(),
            'content' => $this->faker->paragraphs(3, true),
            'excerpt' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }

    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function forPage($page)
    {
        return $this->afterCreating(function (CmsBlock $block) use ($page) {
            \App\Models\CmsBlockPage::create([
                'cms_block_id' => $block->id,
                'page' => $page,
                'position' => 0,
            ]);
        });
    }
}
