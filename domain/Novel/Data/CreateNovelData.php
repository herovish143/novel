<?php

declare(strict_types=1);

namespace Domain\Novel\Data;

use Domain\Novel\Models\Novel;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[TypeScript]
#[MapName(SnakeCaseMapper::class)]
class CreateNovelData extends Data
{
    public function __construct(
        public string $title,
        public string $originalLanguage,
        public string $outputLanguage,
        public ?string $sourceUrl,
        public ?string $description,
        public string $visualStyle,
        public string $narrationStyle,
        public float $maxCostPerEpisode,
    ) {}

    public static function fromModel(Novel $novel): self
    {
        return new self(
            title: $novel->title,
            originalLanguage: $novel->original_language,
            outputLanguage: $novel->output_language,
            sourceUrl: $novel->source_url,
            description: $novel->description,
            visualStyle: $novel->visual_style,
            narrationStyle: $novel->narration_style,
            maxCostPerEpisode: (float) $novel->max_cost_per_episode,
        );
    }

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
