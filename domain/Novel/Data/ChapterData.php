<?php

namespace Domain\Novel\Data;

use Domain\Novel\Models\Chapter;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\TypeScript;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[TypeScript]
#[MapName(SnakeCaseMapper::class)]
class ChapterData extends Data
{
    public function __construct(
        public int $id,
        public int $novelId,
        public int $chapterNumber,
        public string $title,
        public ?string $sourceUrl,
        public string $sourceText,
        public string $sourceHash,
        public string $status,
        public ?string $importedAt,
        public ?string $analyzedAt,
        public ?string $scriptedAt,
    ) {}

    public static function fromModel(Chapter $chapter): self
    {
        return new self(
            id: $chapter->id,
            novelId: $chapter->novel_id,
            chapterNumber: $chapter->chapter_number,
            title: $chapter->title,
            sourceUrl: $chapter->source_url,
            sourceText: $chapter->source_text,
            sourceHash: $chapter->source_hash,
            status: $chapter->status,
            importedAt: $chapter->imported_at?->toIso8601String(),
            analyzedAt: $chapter->analyzed_at?->toIso8601String(),
            scriptedAt: $chapter->scripted_at?->toIso8601String(),
        );
    }
}
