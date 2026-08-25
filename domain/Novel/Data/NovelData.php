<?php

namespace Domain\Novel\Data;

use Domain\Novel\Models\Novel;
use Spatie\LaravelData\Data;

class NovelData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $slug,
        public string $original_language,
        public string $output_language,
        public ?string $source_url,
        public ?string $description,
        public string $visual_style,
        public string $narration_style,
        public float $max_cost_per_episode,
        public string $status,
        public ?int $chapters_count = null,
        public ?int $characters_count = null,
        public ?int $locations_count = null,
    ) {}

    public static function fromModel(Novel $novel): self
    {
        return new self(
            id: $novel->id,
            title: $novel->title,
            slug: $novel->slug,
            original_language: $novel->original_language,
            output_language: $novel->output_language,
            source_url: $novel->source_url,
            description: $novel->description,
            visual_style: $novel->visual_style,
            narration_style: $novel->narration_style,
            max_cost_per_episode: (float) $novel->max_cost_per_episode,
            status: $novel->status,
            chapters_count: $novel->chapters_count ?? null,
            characters_count: $novel->characters_count ?? null,
            locations_count: $novel->locations_count ?? null,
        );
    }
}
