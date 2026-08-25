<?php

namespace Database\Factories;

use Domain\Novel\Models\Chapter;
use Domain\Novel\Models\Novel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Chapter>
 */
class ChapterFactory extends Factory
{
    protected $model = Chapter::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sourceText = fake()->paragraphs(5, true);

        return [
            'novel_id' => Novel::factory(),
            'chapter_number' => fake()->unique()->numberBetween(1, 1000),
            'title' => fake()->sentence(4),
            'source_url' => fake()->url(),
            'source_text' => $sourceText,
            'source_hash' => hash('sha256', $sourceText),
            'status' => 'IMPORTED',
            'imported_at' => now(),
        ];
    }
}
