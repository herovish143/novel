<?php

namespace Domain\Novel\Data;

use Domain\Novel\Models\Chapter;
use Spatie\LaravelData\Data;

class ChapterData extends Data
{
    public function __construct(
        public int $id,
        public int $novel_id,
        public int $chapter_number,
        public string $title,
        public ?string $source_url,
        public string $source_text,
        public string $source_hash,
        public string $status,
        public ?string $imported_at,
        public ?string $analyzed_at,
        public ?string $scripted_at,
    ) {}

    public static function fromModel(Chapter $chapter): self
    {
        return new self(
            id: $chapter->id,
            novel_id: $chapter->novel_id,
            chapter_number: $chapter->chapter_number,
            title: $chapter->title,
            source_url: $chapter->source_url,
            source_text: $chapter->source_text,
            source_hash: $chapter->source_hash,
            status: $chapter->status,
            imported_at: $chapter->imported_at?->toIso8601String(),
            analyzed_at: $chapter->analyzed_at?->toIso8601String(),
            scripted_at: $chapter->scripted_at?->toIso8601String(),
        );
    }
}
