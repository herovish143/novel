<?php

declare(strict_types=1);

namespace Domain\Novel\Data;

use Spatie\LaravelData\Data;

class CreateNovelData extends Data
{
    public function __construct(
        public string $title,
        public string $original_language,
        public string $output_language,
        public ?string $source_url,
        public ?string $description,
        public string $visual_style,
        public string $narration_style,
        public float $max_cost_per_episode,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'original_language' => ['required', 'string', 'max:10'],
            'output_language' => ['required', 'string', 'max:10'],
            'source_url' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string'],
            'visual_style' => ['required', 'string', 'max:255'],
            'narration_style' => ['required', 'string', 'max:255'],
            'max_cost_per_episode' => ['required', 'numeric', 'min:0.5'],
        ];
    }
}
