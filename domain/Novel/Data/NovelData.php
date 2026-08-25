<?php

namespace Domain\Novel\Data;

use Domain\Novel\Models\Novel;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
#[MapName(SnakeCaseMapper::class)]
class NovelData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $slug,
        public string $originalLanguage,
        public string $outputLanguage,
        public ?string $sourceUrl,
        public ?string $description,
        public string $visualStyle,
        public string $narrationStyle,
        public float $maxCostPerEpisode,
        public string $status,
        public ?int $chaptersCount = null,
        public ?int $charactersCount = null,
        public ?int $locationsCount = null,
    ) {}

    public static function fromModel(Novel $novel): self
    {
        return new self(
            id: $novel->id,
            title: $novel->title,
            slug: $novel->slug,
            originalLanguage: $novel->original_language,
            outputLanguage: $novel->output_language,
            sourceUrl: $novel->source_url,
            description: $novel->description,
            visualStyle: $novel->visual_style,
            narrationStyle: $novel->narration_style,
            maxCostPerEpisode: (float) $novel->max_cost_per_episode,
            status: $novel->status,
            chaptersCount: $novel->chapters_count ?? null,
            charactersCount: $novel->characters_count ?? null,
            locationsCount: $novel->locations_count ?? null,
        );
    }
}
