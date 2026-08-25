<?php

namespace Database\Factories;

use Domain\Novel\Models\Novel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Novel>
 */
class NovelFactory extends Factory
{
    protected $model = Novel::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->randomNumber(4),
            'original_language' => 'en',
            'output_language' => 'hi',
            'source_url' => fake()->url(),
            'description' => fake()->paragraph(),
            'visual_style' => 'dark cinematic fantasy',
            'narration_style' => 'conversational Hindi explanation',
            'max_cost_per_episode' => 5.00,
            'status' => 'ACTIVE',
        ];
    }
}
